<?php

namespace App\implementations;

use App\Interfaces\icustomercontactreportInterface;
use App\Jobs\SendBulkEmailJob;
use App\Jobs\SendBulkSmsJob;
use App\Models\Customer;

class _customercontactreportRepository implements icustomercontactreportInterface
{
    protected $customer;

    public function __construct(Customer $customer)
    {
        $this->customer = $customer;
    }

    /**
     * A customer is COMPLIANT when they have an APPROVED renewal/registration
     * application (applicationtype_id 1, 2 or 3) for the current year.
     */
    private function complianceSql(): string
    {
        return 'EXISTS (SELECT 1 FROM customerapplications ca '
            ."WHERE ca.customer_id = customers.id "
            .'AND ca.applicationtype_id IN (1, 2, 3) '
            ."AND ca.status = 'APPROVED' "
            .'AND ca.year = ?)';
    }

    /**
     * Apply the filters that are common to listing, summary and exports
     * (everything except the compliance status and the channel requirement).
     */
    private function baseQuery(array $filters)
    {
        $query = $this->customer->newQuery();

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('surname', 'like', "%{$search}%")
                    ->orWhere('regnumber', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if (! empty($filters['province_id'])) {
            $query->where('province_id', $filters['province_id']);
        }

        if (! empty($filters['profession_id'])) {
            $query->whereHas('customerprofessions', function ($q) use ($filters) {
                $q->where('profession_id', $filters['profession_id']);
            });
        }

        if (! empty($filters['registertype_id'])) {
            $query->whereHas('customerprofessions', function ($q) use ($filters) {
                $q->where('registertype_id', $filters['registertype_id']);
            });
        }

        if (! empty($filters['selected']) && is_array($filters['selected'])) {
            $query->whereIn('id', $filters['selected']);
        }

        return $query;
    }

    private function applyChannel($query, array $filters): void
    {
        $channel = $filters['channel'] ?? null;
        if ($channel === 'email') {
            $query->whereNotNull('email')->where('email', '!=', '');
        } elseif ($channel === 'phone') {
            $query->whereNotNull('phone')->where('phone', '!=', '');
        }
    }

    private function applyCompliance($query, array $filters, $year, string $sql): void
    {
        $compliance = $filters['compliance'] ?? null;
        if ($compliance === 'compliant') {
            $query->whereRaw($sql, [$year]);
        } elseif ($compliance === 'noncompliant') {
            $query->whereRaw('NOT '.$sql, [$year]);
        }
    }

    private function listQuery(array $filters)
    {
        $year = $filters['year'] ?? date('Y');
        $sql = $this->complianceSql();

        $query = $this->baseQuery($filters)
            ->select('customers.*')
            ->selectRaw($sql.' as is_compliant', [$year])
            ->with(['province:id,name', 'city:id,name']);

        $this->applyChannel($query, $filters);
        $this->applyCompliance($query, $filters, $year, $sql);

        return $query->orderBy('surname')->orderBy('name');
    }

    public function getContacts(array $filters, int $perPage = 25)
    {
        return $this->listQuery($filters)->paginate($perPage);
    }

    public function getContactsList(array $filters)
    {
        return $this->listQuery($filters)->get();
    }

    public function getSummary(array $filters)
    {
        $year = $filters['year'] ?? date('Y');
        $sql = $this->complianceSql();

        $total = $this->baseQuery($filters)->count();
        $compliant = $this->baseQuery($filters)->whereRaw($sql, [$year])->count();
        $withEmail = $this->baseQuery($filters)->whereNotNull('email')->where('email', '!=', '')->count();
        $withPhone = $this->baseQuery($filters)->whereNotNull('phone')->where('phone', '!=', '')->count();

        return [
            'total' => $total,
            'compliant' => $compliant,
            'noncompliant' => $total - $compliant,
            'with_email' => $withEmail,
            'with_phone' => $withPhone,
        ];
    }

    /**
     * Personalization tokens for a single customer.
     */
    private function tokens(Customer $customer): array
    {
        return [
            '{name}' => $customer->name ?? '',
            '{surname}' => $customer->surname ?? '',
            '{fullname}' => trim(($customer->name ?? '').' '.($customer->surname ?? '')),
            '{regnumber}' => $customer->regnumber ?? '',
        ];
    }

    /**
     * Queue personalized emails for background, batched delivery.
     * Returns immediately; the SendBulkEmailJob does the work off the request.
     */
    public function sendBulkEmail(array $filters, string $subject, string $message)
    {
        $filters['channel'] = 'email';
        $contacts = $this->getContactsList($filters);

        $recipients = [];
        $seen = [];
        foreach ($contacts as $customer) {
            $email = trim((string) $customer->email);
            if ($email === '' || isset($seen[strtolower($email)])) {
                continue;
            }
            $seen[strtolower($email)] = true;
            $recipients[] = ['email' => $email, 'tokens' => $this->tokens($customer)];
        }

        if (empty($recipients)) {
            return ['status' => 'error', 'message' => 'No contacts with an email address matched the current filters.'];
        }

        // One job per 1000 recipients (SendGrid's per-call personalization limit).
        $body = nl2br($message);
        foreach (array_chunk($recipients, 1000) as $chunk) {
            SendBulkEmailJob::dispatch($chunk, $subject, $body);
        }

        return [
            'status' => 'success',
            'message' => 'Queued '.count($recipients).' email(s) for background sending.',
            'queued' => count($recipients),
        ];
    }

    /**
     * Queue personalized SMS for background, concurrent delivery.
     */
    public function sendBulkSms(array $filters, string $message)
    {
        $filters['channel'] = 'phone';
        $contacts = $this->getContactsList($filters);

        $messages = [];
        foreach ($contacts as $customer) {
            $phone = trim((string) $customer->phone);
            if ($phone === '') {
                continue;
            }
            $messages[] = ['phone' => $phone, 'message' => strtr($message, $this->tokens($customer))];
        }

        if (empty($messages)) {
            return ['status' => 'error', 'message' => 'No contacts with a phone number matched the current filters.'];
        }

        // One job per 200 messages; each job sends in concurrent batches of 50.
        foreach (array_chunk($messages, 200) as $chunk) {
            SendBulkSmsJob::dispatch($chunk);
        }

        return [
            'status' => 'success',
            'message' => 'Queued '.count($messages).' SMS for background sending.',
            'queued' => count($messages),
        ];
    }
}
