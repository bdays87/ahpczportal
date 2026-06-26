<?php

namespace App\Livewire\Admin;

use App\Interfaces\icustomercontactreportInterface;
use App\Models\Profession;
use App\Models\Province;
use App\Models\Registertype;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Mary\Traits\Toast;

class Customercontactreport extends Component
{
    use Toast, WithFileUploads, WithPagination;

    public string $tab = 'email';

    public $search;

    public $compliance;

    public $province_id;

    public $profession_id;

    public $registertype_id;

    public $year;

    public array $selected = [];

    // Email composer
    public bool $emailModal = false;

    public $emailSubject;

    public $emailBody;

    // Email service provider: 'default' (SendGrid/SMTP) or 'nhume'
    public string $emailProvider = 'default';

    public $nhumeCredits = null;

    public $nhumeError = null;

    public $emailCc;

    public $emailAttachments = [];

    // Extra recipient emails uploaded from a CSV
    public $recipientCsv;

    // SMS composer
    public bool $smsModal = false;

    public $smsBody;

    public $breadcrumbs = [];

    protected $contactRepo;

    public function boot(icustomercontactreportInterface $contactRepo): void
    {
        $this->contactRepo = $contactRepo;
    }

    public function mount(): void
    {
        $this->year = date('Y');
        $this->breadcrumbs = [
            ['label' => 'Dashboard', 'icon' => 'o-home', 'link' => route('dashboard')],
            ['label' => 'Customer Contacts'],
        ];
    }

    /**
     * Build the filter array passed to the repository.
     * `channel` mirrors the active tab so each tab only shows usable contacts.
     */
    private function filters(bool $withSelection = false): array
    {
        $filters = [
            'search' => $this->search,
            'compliance' => $this->compliance,
            'province_id' => $this->province_id,
            'profession_id' => $this->profession_id,
            'registertype_id' => $this->registertype_id,
            'year' => $this->year,
            'channel' => $this->tab,
        ];

        if ($withSelection && ! empty($this->selected)) {
            $filters['selected'] = $this->selected;
        }

        return $filters;
    }

    public function updated($property): void
    {
        if (in_array($property, ['search', 'compliance', 'province_id', 'profession_id', 'registertype_id', 'year', 'tab'])) {
            $this->resetPage();
            $this->selected = [];
        }
    }

    public function setTab(string $tab): void
    {
        $this->tab = $tab;
        $this->resetPage();
        $this->selected = [];
    }

    public function clearFilters(): void
    {
        $this->reset(['search', 'compliance', 'province_id', 'profession_id', 'registertype_id', 'selected']);
        $this->year = date('Y');
        $this->resetPage();
    }

    public function complianceOptions(): array
    {
        return [
            ['id' => '', 'name' => 'All'],
            ['id' => 'compliant', 'name' => 'Compliant'],
            ['id' => 'noncompliant', 'name' => 'Non-compliant'],
        ];
    }

    public function headers(): array
    {
        $headers = [
            ['key' => 'selection', 'label' => '', 'sortable' => false],
            ['key' => 'regnumber', 'label' => 'Reg #'],
            ['key' => 'fullname', 'label' => 'Name'],
        ];

        if ($this->tab === 'email') {
            $headers[] = ['key' => 'email', 'label' => 'Email'];
        } else {
            $headers[] = ['key' => 'phone', 'label' => 'Phone'];
        }

        $headers[] = ['key' => 'province', 'label' => 'Province'];
        $headers[] = ['key' => 'compliance', 'label' => 'Status'];

        return $headers;
    }

    /* ---------------------------------------------------------------------
     |  Exports
     * ------------------------------------------------------------------- */

    private function exportRows()
    {
        return $this->contactRepo->getContactsList($this->filters(true));
    }

    private function exportColumns(): array
    {
        return ['Reg Number', 'First Name', 'Surname', 'Email', 'Phone', 'Province', 'City', 'Compliance Status'];
    }

    private function rowValues($customer): array
    {
        return [
            $customer->regnumber ?? 'N/A',
            $customer->name ?? '',
            $customer->surname ?? '',
            $customer->email ?? '',
            $customer->phone ?? '',
            $customer->province?->name ?? 'N/A',
            $customer->city?->name ?? 'N/A',
            $customer->is_compliant ? 'Compliant' : 'Non-compliant',
        ];
    }

    public function exportCsv()
    {
        $rows = $this->exportRows();
        if ($rows->isEmpty()) {
            $this->warning('No contacts to export.');

            return null;
        }

        $filename = 'customer_contacts_'.date('Y-m-d_His').'.csv';
        $filepath = storage_path('app/public/'.$filename);
        $file = fopen($filepath, 'w');

        fputcsv($file, $this->exportColumns());
        foreach ($rows as $customer) {
            fputcsv($file, $this->rowValues($customer));
        }
        fclose($file);

        return response()->download($filepath)->deleteFileAfterSend(true);
    }

    /**
     * Export just the active tab's channel (emails or phone numbers) as a simple
     * one-column CSV, ready to upload as a recipient list elsewhere.
     */
    public function exportUploadList()
    {
        $rows = $this->exportRows();
        if ($rows->isEmpty()) {
            $this->warning('No contacts to export.');

            return null;
        }

        $isEmail = $this->tab === 'email';
        $header = $isEmail ? 'email' : 'phone';
        $filename = ($isEmail ? 'emails' : 'phones').'_for_upload_'.date('Y-m-d_His').'.csv';
        $filepath = storage_path('app/public/'.$filename);
        $file = fopen($filepath, 'w');

        fputcsv($file, [$header]);
        $seen = [];
        foreach ($rows as $customer) {
            $value = trim((string) ($isEmail ? $customer->email : $customer->phone));
            if ($value === '' || isset($seen[strtolower($value)])) {
                continue;
            }
            $seen[strtolower($value)] = true;
            fputcsv($file, [$value]);
        }
        fclose($file);

        return response()->download($filepath)->deleteFileAfterSend(true);
    }

    public function exportExcel()
    {
        $rows = $this->exportRows();
        if ($rows->isEmpty()) {
            $this->warning('No contacts to export.');

            return null;
        }

        $title = ($this->compliance === 'noncompliant' ? 'Non-compliant ' : '').'Customer Contacts';

        $html = '<html><head><meta charset="UTF-8"></head><body>';
        $html .= '<table border="1" cellspacing="0" cellpadding="6" style="border-collapse:collapse;font-family:Arial,sans-serif;font-size:12px;">';
        $html .= '<thead><tr>';
        foreach ($this->exportColumns() as $col) {
            $html .= '<th style="background-color:#1d4ed8;color:#ffffff;font-weight:bold;text-align:left;border:1px solid #1e40af;">'.e($col).'</th>';
        }
        $html .= '</tr></thead><tbody>';
        foreach ($rows as $customer) {
            $html .= '<tr>';
            foreach ($this->rowValues($customer) as $value) {
                $html .= '<td style="border:1px solid #cccccc;">'.e($value).'</td>';
            }
            $html .= '</tr>';
        }
        $html .= '</tbody></table></body></html>';

        $filename = 'customer_contacts_'.date('Y-m-d_His').'.xls';
        $filepath = storage_path('app/public/'.$filename);
        file_put_contents($filepath, $html);

        return response()->download($filepath, $filename, [
            'Content-Type' => 'application/vnd.ms-excel',
        ])->deleteFileAfterSend(true);
    }

    public function exportPdf()
    {
        $rows = $this->exportRows();
        if ($rows->isEmpty()) {
            $this->warning('No contacts to export.');

            return null;
        }

        $title = ($this->compliance === 'noncompliant' ? 'Non-compliant ' : '').'Customer Contacts';

        $pdf = Pdf::loadView('exports.customercontacts', [
            'title' => $title,
            'columns' => $this->exportColumns(),
            'rows' => $rows->map(fn ($c) => $this->rowValues($c)),
            'generatedAt' => now()->format('d M Y H:i'),
        ])->setPaper('a4', 'landscape');

        return response()->streamDownload(
            fn () => print($pdf->stream()),
            'customer_contacts_'.date('Y-m-d_His').'.pdf'
        );
    }

    /* ---------------------------------------------------------------------
     |  Sending
     * ------------------------------------------------------------------- */

    public function emailProviderOptions(): array
    {
        return [
            ['id' => 'default', 'name' => 'Default (SendGrid / SMTP)'],
            ['id' => 'nhume', 'name' => 'Nhume'],
        ];
    }

    public function openEmailModal(): void
    {
        $this->reset(['emailSubject', 'emailBody', 'emailCc', 'emailAttachments', 'recipientCsv']);
        $this->emailProvider = 'default';
        $this->nhumeCredits = null;
        $this->nhumeError = null;
        $this->emailModal = true;
    }

    /** Extract valid email addresses from an uploaded CSV (any column, header ignored). */
    private function parseCsvEmails($file): array
    {
        $emails = [];
        $handle = fopen($file->getRealPath(), 'r');
        if ($handle === false) {
            return $emails;
        }
        while (($row = fgetcsv($handle, 0, ',')) !== false) {
            foreach ($row as $cell) {
                $cell = trim((string) $cell);
                if ($cell !== '' && filter_var($cell, FILTER_VALIDATE_EMAIL)) {
                    $emails[] = $cell;
                }
            }
        }
        fclose($handle);

        return array_values(array_unique($emails));
    }

    public function updatedEmailProvider($value): void
    {
        if ($value === 'nhume') {
            $this->refreshNhumeCredits();
        }
    }

    /** Fetch remaining Nhume transactional credits for display. */
    public function refreshNhumeCredits(): void
    {
        $this->nhumeCredits = null;
        $this->nhumeError = null;
        try {
            $nhume = app(\App\Services\Nhume::class);
            if (! $nhume->configured()) {
                $this->nhumeError = 'Nhume is not configured (set NHUME_API_KEY and NHUME_FROM).';

                return;
            }
            $this->nhumeCredits = $nhume->remainingCredits('TRANSACTIONAL');
        } catch (\Throwable $e) {
            $this->nhumeError = 'Could not read Nhume credits: '.$e->getMessage();
        }
    }

    public function sendEmail()
    {
        $this->validate([
            'emailSubject' => 'required|string|max:255',
            'emailBody' => 'required|string',
            'emailProvider' => 'required|in:default,nhume',
            // CC is optional; allow a comma-separated list of emails.
            'emailCc' => ['nullable', 'string', function ($attribute, $value, $fail) {
                foreach (preg_split('/[,;]+/', $value, -1, PREG_SPLIT_NO_EMPTY) as $addr) {
                    if (! filter_var(trim($addr), FILTER_VALIDATE_EMAIL)) {
                        $fail('CC contains an invalid email address: '.trim($addr));
                    }
                }
            }],
            // Attachments are optional; if present each must be an allowed type and within size.
            'emailAttachments' => 'nullable|array|max:5',
            'emailAttachments.*' => 'file|mimes:pdf,ppt,pptx,doc,docx,png,jpg,jpeg|max:5120',
            'recipientCsv' => 'nullable|file|mimes:csv,txt|max:10240',
        ], [
            'emailAttachments.*.mimes' => 'Allowed attachment types: pdf, ppt, pptx, doc, docx, png, jpg.',
            'emailAttachments.*.max' => 'Each attachment must be 5 MB or smaller.',
        ]);

        // Store attachments on the public disk so the queued job can read them.
        $attachmentPaths = [];
        foreach ($this->emailAttachments as $file) {
            $attachmentPaths[] = $file->store('email-attachments', 'public');
        }

        // Parse any extra recipients uploaded from CSV.
        $extraEmails = $this->recipientCsv ? $this->parseCsvEmails($this->recipientCsv) : [];

        $cc = $this->emailCc ? trim($this->emailCc) : null;
        if ($this->emailProvider === 'nhume' && ($cc || ! empty($attachmentPaths))) {
            $this->warning('Note: Nhume does not support CC or attachments — they will be ignored for this provider.');
        }

        $response = $this->contactRepo->sendBulkEmail(
            $this->filters(true),
            $this->emailSubject,
            $this->emailBody,
            $this->emailProvider,
            $cc,
            $attachmentPaths,
            $extraEmails
        );

        if ($response['status'] === 'success') {
            $this->success($response['message']);
        } else {
            $this->error($response['message']);
        }
        $this->emailModal = false;
        $this->reset(['emailAttachments', 'emailCc', 'recipientCsv']);
    }

    /** Number of queued broadcast jobs waiting to be processed. */
    public function pendingJobs(): int
    {
        try {
            return DB::table('jobs')->count();
        } catch (\Throwable $e) {
            return 0;
        }
    }

    /**
     * Drain the queue from the browser so emails/SMS actually go out even when
     * no background worker is running.
     */
    public function processQueue(): void
    {
        try {
            @set_time_limit(0);
            $before = $this->pendingJobs();
            Artisan::call('app:send-pending-emails', ['--max-time' => 30]);
            $after = $this->pendingJobs();
            $processed = max(0, $before - $after);

            if ($after > 0) {
                $this->warning("Processed {$processed} job(s). {$after} still pending — click again to continue.");
            } else {
                $this->success($processed > 0 ? "Processed {$processed} job(s). Queue is empty." : 'Queue is already empty.');
            }
        } catch (\Throwable $e) {
            $this->error('Could not process the queue: '.$e->getMessage());
        }
    }

    /** Delete every pending and failed job from the queue (discards unsent work). */
    public function clearQueue(): void
    {
        try {
            $jobs = DB::table('jobs')->count();
            $failed = DB::table('failed_jobs')->count();
            DB::table('jobs')->delete();
            DB::table('failed_jobs')->delete();
            $this->success("Cleared {$jobs} pending and {$failed} failed job(s) from the queue.");
        } catch (\Throwable $e) {
            $this->error('Could not clear the queue: '.$e->getMessage());
        }
    }

    public function openSmsModal(): void
    {
        $this->reset(['smsBody']);
        $this->smsModal = true;
    }

    public function sendSms()
    {
        $this->validate([
            'smsBody' => 'required|string|max:480',
        ]);

        $response = $this->contactRepo->sendBulkSms($this->filters(true), $this->smsBody);

        if ($response['status'] === 'success') {
            $this->success($response['message']);
        } else {
            $this->error($response['message']);
        }
        $this->smsModal = false;
    }

    public function render()
    {
        return view('livewire.admin.customercontactreport', [
            'contacts' => $this->contactRepo->getContacts($this->filters(true)),
            'summary' => $this->contactRepo->getSummary($this->filters()),
            'headers' => $this->headers(),
            'provinces' => Province::orderBy('name')->get(),
            'professions' => Profession::orderBy('name')->get(),
            'registertypes' => Registertype::orderBy('name')->get(),
            'complianceOptions' => $this->complianceOptions(),
            'emailProviderOptions' => $this->emailProviderOptions(),
        ]);
    }
}
