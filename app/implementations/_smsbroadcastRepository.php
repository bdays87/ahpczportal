<?php

namespace App\implementations;

use App\Interfaces\ismsbroadcastInterface;
use App\Models\Customer;
use App\Models\Smsbroadcast;
use App\Models\Smsbroadcastrecipient;
use App\Models\Smscredit;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class _smsbroadcastRepository implements ismsbroadcastInterface
{
    protected $smscredit;

    protected $smsbroadcast;

    protected $recipient;

    protected $customer;

    public function __construct(
        Smscredit $smscredit,
        Smsbroadcast $smsbroadcast,
        Smsbroadcastrecipient $recipient,
        Customer $customer
    ) {
        $this->smscredit = $smscredit;
        $this->smsbroadcast = $smsbroadcast;
        $this->recipient = $recipient;
        $this->customer = $customer;
    }

    public function addCredits(array $data)
    {
        $data['addedby'] = Auth::id();
        $data['used_credits'] = 0;
        $data['remaining_credits'] = $data['credits'];

        return $this->smscredit->create($data);
    }

    public function getTotalCredits()
    {
        return $this->smscredit->sum('credits');
    }

    public function getUsedCredits()
    {
        return $this->smsbroadcast->sum('credits_used');
    }

    public function getRemainingCredits()
    {
        return $this->getTotalCredits() - $this->getUsedCredits();
    }

    public function getCreditHistory()
    {
        return $this->smscredit->with('addedBy')->orderBy('created_at', 'desc')->get();
    }

    public function createCampaign(array $data)
    {
        $data['createdby'] = Auth::id();
        $data['status']    = 'DRAFT';

        // Store provider and test numbers on the campaign record
        $campaign = $this->smsbroadcast->create([
            'campaign_name' => $data['campaign_name'],
            'message'       => $data['message'],
            'status'        => 'DRAFT',
            'createdby'     => Auth::id(),
            'provider'      => $data['provider'] ?? config('services.sms_provider', 'esolutions'),
            'test_numbers'  => $data['test_numbers'] ?? null,
            'filters'       => json_encode($data['filters'] ?? []),
        ]);

        // Build recipient list from DB filters OR uploaded file — NEVER BOTH
        if (($data['contact_source'] ?? 'db') === 'file') {
            // File mode — ONLY use imported contacts, ignore DB entirely
            $contacts = $data['imported_contacts'] ?? [];
            if (empty($contacts)) {
                // No contacts loaded — abort rather than fall through to DB
                $campaign->delete();
                throw new \Exception('No contacts found in the uploaded file. Please load contacts first.');
            }
            foreach ($contacts as $phone) {
                $phone = trim($phone);
                if ($phone) {
                    $this->recipient->create([
                        'smsbroadcast_id' => $campaign->id,
                        'customer_id'     => null,
                        'phone'           => $phone,
                        'status'          => 'PENDING',
                    ]);
                }
            }
        } else {
            // DB mode — use filters only
            $recipients = $this->getFilteredRecipients($data['filters'] ?? []);
            foreach ($recipients as $customer) {
                if ($customer->phone) {
                    $this->recipient->create([
                        'smsbroadcast_id' => $campaign->id,
                        'customer_id'     => $customer->id,
                        'phone'           => $customer->phone,
                        'status'          => 'PENDING',
                    ]);
                }
            }
        }

        $campaign->update(['total_recipients' => $campaign->recipients()->count()]);

        return $campaign;
    }

    public function getCampaigns()
    {
        return $this->smsbroadcast
            ->with('creator', 'recipients')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getCampaignById($id)
    {
        return $this->smsbroadcast
            ->with('creator', 'recipients.customer')
            ->findOrFail($id);
    }

    public function getFilteredRecipients(array $filters)
    {
        $query = $this->customer->whereHas('customerprofessions');

        // Filter by compliance status
        if (! empty($filters['compliance'])) {
            if ($filters['compliance'] === 'Valid') {
                $query->whereHas('customerprofessions.applications', function ($q) {
                    $q->where('status', 'APPROVED')
                        ->where('certificate_expiry_date', '>', now());
                });
            } elseif ($filters['compliance'] === 'Expired') {
                $query->whereHas('customerprofessions.applications', function ($q) {
                    $q->where('status', 'APPROVED')
                        ->where('certificate_expiry_date', '<=', now());
                });
            }
        }

        // Filter by profession
        if (! empty($filters['profession_id'])) {
            $query->whereHas('customerprofessions', function ($q) use ($filters) {
                $q->where('profession_id', $filters['profession_id']);
            });
        }

        // Filter by register type
        if (! empty($filters['registertype_id'])) {
            $query->whereHas('customerprofessions', function ($q) use ($filters) {
                $q->where('registertype_id', $filters['registertype_id']);
            });
        }

        // Filter by province
        if (! empty($filters['province_id'])) {
            $query->where('province_id', $filters['province_id']);
        }

        // Filter by city
        if (! empty($filters['city_id'])) {
            $query->where('city_id', $filters['city_id']);
        }

        return $query->whereNotNull('phone')->get();
    }

    public function sendBroadcast($campaignId)
    {
        $campaign = $this->getCampaignById($campaignId);

        // Check if enough credits
        $remainingCredits = $this->getRemainingCredits();
        if ($remainingCredits < $campaign->pending_count) {
            return [
                'status' => 'error',
                'message' => 'Insufficient SMS credits. Need '.$campaign->pending_count.' credits, have '.$remainingCredits,
            ];
        }

        // Update campaign status to SENDING
        $campaign->update(['status' => 'SENDING']);

        // Get pending recipients
        $pendingRecipients = $campaign->recipients()->where('status', 'PENDING')->get();

        $sentCount = 0;
        $failedCount = 0;

        foreach ($pendingRecipients as $recipient) {
            try {
                $result = $this->sendSMS($recipient->phone, $campaign->message);
                if ($result && $result !== false) {
                    $recipient->update([
                        'status'              => 'SENT',
                        'sent_at'             => now(),
                        'provider_message_id' => is_string($result) ? $result : null,
                    ]);
                    $sentCount++;
                } else {
                    $recipient->update([
                        'status'        => 'FAILED',
                        'error_message' => 'Failed to send SMS',
                    ]);
                    $failedCount++;
                }
            } catch (\Exception $e) {
                $failedCount++;
            }
        }

        // Refresh campaign from DB to get accurate counts
        $campaign->refresh();

        // Update campaign statistics — always mark SENT when done
        $totalSent   = $campaign->recipients()->where('status', 'SENT')->count();
        $totalFailed = $campaign->recipients()->where('status', 'FAILED')->count();

        $campaign->update([
            'sent_count'   => $totalSent,
            'failed_count' => $totalFailed,
            'credits_used' => $totalSent,
            'status'       => 'SENT',
        ]);

        return [
            'status' => 'success',
            'message' => "Sent {$sentCount} SMS messages, {$failedCount} failed",
            'sent' => $sentCount,
            'failed' => $failedCount,
        ];
    }

    private function sendSMS($phone, $message)
    {
        $provider = config('services.sms_provider', 'esolutions');

        return match ($provider) {
            'nhume'      => $this->sendViaNhume($phone, $message),
            'twilio'     => $this->sendViaTwilio($phone, $message),
            'africastalking' => $this->sendViaAfricasTalking($phone, $message),
            default      => $this->sendViaESolutions($phone, $message),
        };
    }

    public function sendTestSms(string $phone, string $message)
    {
        try {
            $result = $this->sendSMS($phone, $message);
            return ['status' => $result ? 'success' : 'error', 'message' => $result ? 'Test SMS sent successfully.' : 'Failed to send test SMS.'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function getNhumeBalance()
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.config('services.nhume.api_key'),
                'Accept'        => 'application/json',
            ])->get('https://api.nhume.co.zw/api/v1/sms/balance');

            if ($response->successful()) {
                return $response->json('data.balance', 0);
            }
        } catch (\Exception $e) {
            Log::error('Nhume balance check failed: '.$e->getMessage());
        }
        return null;
    }

    public function parsePhoneContent(string $content, string $ext = 'txt'): array
    {
        $phones  = [];
        $content = ltrim($content, "\xEF\xBB\xBF"); // strip BOM

        if ($ext === 'csv') {
            $lines = preg_split('/\r\n|\r|\n/', $content);
            $first = true;
            foreach ($lines as $line) {
                $cells = str_getcsv($line);
                $cell  = trim($cells[0] ?? '');
                if ($first && ! preg_match('/^[0-9+]/', $cell)) {
                    $first = false;
                    continue; // skip header
                }
                $first = false;
                $phone = preg_replace('/[^0-9+]/', '', $cell);
                if (strlen($phone) >= 9) {
                    $phones[] = $phone;
                }
            }
        } else {
            // Normalise all separators to space
            $content = preg_replace('/[\r\n,;|]+/', ' ', $content);
            $content = preg_replace('/\s+/', ' ', $content);
            $tokens  = explode(' ', trim($content));

            foreach ($tokens as $token) {
                $clean = preg_replace('/[^0-9+]/', '', trim($token));
                if (empty($clean)) continue;

                if (strlen($clean) > 15) {
                    preg_match_all('/263\d{9}/', $clean, $m1);
                    foreach ($m1[0] as $n) { $phones[] = $n; }
                    preg_match_all('/07\d{8}/', $clean, $m2);
                    foreach ($m2[0] as $n) { $phones[] = $n; }
                } elseif (strlen($clean) >= 9) {
                    $phones[] = $clean;
                }
            }
        }

        return array_values(array_unique($phones));
    }

    public function importContactsFromFile(string $filePath): array
    {
        $phones   = [];
        $fullPath = storage_path('app/'.$filePath);

        if (! file_exists($fullPath)) {
            return [];
        }

        $ext = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));

        if ($ext === 'csv') {
            // CSV: read first column of each row, skip header if it looks like text
            $handle = fopen($fullPath, 'r');
            $first  = true;
            while (($row = fgetcsv($handle)) !== false) {
                $cell = trim($row[0] ?? '');
                // Skip header row (non-numeric first cell)
                if ($first && ! preg_match('/^[0-9+]/', $cell)) {
                    $first = false;
                    continue;
                }
                $first = false;
                $phone = preg_replace('/[^0-9+]/', '', $cell);
                if (strlen($phone) >= 9) {
                    $phones[] = $phone;
                }
            }
            fclose($handle);
        } else {
            // TXT: handle all common formats:
            // - one per line
            // - comma/semicolon/pipe separated on one line
            // - concatenated without separators (263XXXXXXXXX)
            $content = file_get_contents($fullPath);
            $content = ltrim($content, "\xEF\xBB\xBF"); // strip BOM

            // Normalise: replace newlines, commas, semicolons, pipes, trailing commas → space
            $content = preg_replace('/[\r\n,;|]+/', ' ', $content);
            // Also handle trailing commas before newlines
            $content = preg_replace('/,\s*/', ' ', $content);
            $tokens  = preg_split('/\s+/', $content, -1, PREG_SPLIT_NO_EMPTY);

            foreach ($tokens as $token) {
                $clean = preg_replace('/[^0-9+]/', '', trim($token));

                if (empty($clean)) continue;

                if (strlen($clean) > 15) {
                    // Concatenated numbers — split by known patterns
                    preg_match_all('/263\d{9}/', $clean, $m1);
                    foreach ($m1[0] as $n) { $phones[] = $n; }

                    preg_match_all('/07\d{8}/', $clean, $m2);
                    foreach ($m2[0] as $n) { $phones[] = $n; }
                } elseif (strlen($clean) >= 9) {
                    $phones[] = $clean;
                }
            }
        }

        return array_values(array_unique($phones));
    }

    private function sendViaNhume($phone, $message)
    {
        $apiKey = config('services.nhume.api_key');
        $sender = config('services.nhume.sender');

        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$apiKey}",
                'Content-Type'  => 'application/json',
                'Accept'        => 'application/json',
            ])->post('https://api.nhume.co.zw/api/v1/sms/send', [
                'from'    => $sender,
                'to'      => $phone,
                'message' => $message,
            ]);

            if ($response->successful()) {
                $data = $response->json('data');
                // Return message ID so it can be stored per recipient
                return $data['id'] ?? true;
            }

            Log::error('Nhume SMS failed: '.$response->body());
            return false;
        } catch (\Exception $e) {
            Log::error('Nhume SMS exception: '.$e->getMessage());
            return false;
        }
    }

    private function sendViaNhumeBulk(array $recipients, $message)
    {
        $apiKey = config('services.nhume.api_key');
        $sender = config('services.nhume.sender');

        $chunks = array_chunk($recipients, 1000);
        $sent   = 0;
        $failed = 0;

        foreach ($chunks as $chunk) {
            try {
                $response = Http::withHeaders([
                    'Authorization' => "Bearer {$apiKey}",
                    'Content-Type'  => 'application/json',
                    'Accept'        => 'application/json',
                ])->post('https://api.nhume.co.zw/api/v1/sms/send/bulk', [
                    'from'       => $sender,
                    'message'    => $message,
                    'recipients' => array_map(fn ($r) => ['to' => $r], $chunk),
                ]);

                if ($response->successful()) {
                    $sent += $response->json('accepted', 0);
                } else {
                    $failed += count($chunk);
                }
            } catch (\Exception $e) {
                $failed += count($chunk);
                Log::error('Nhume bulk SMS failed: '.$e->getMessage());
            }
        }

        return ['sent' => $sent, 'failed' => $failed];
    }

    /**
     * Send a single SMS immediately (no campaign/credit tracking).
     * Reuses the same SMS gateway configuration as broadcasts.
     */
    public function sendSingleSms($phone, $message)
    {
        return $this->sendSMS($phone, $message);
    }

    /**
     * Send many SMS messages concurrently using an HTTP connection pool.
     * Requests are fired in concurrent batches instead of one-by-one,
     * which dramatically reduces total send time over the eSolutions gateway.
     *
     * @param  array  $messages  [['phone' => '..', 'message' => '..'], ...]
     */
    public function sendBatchSms(array $messages)
    {
        $username = config('services.esolutions.username');
        $password = config('services.esolutions.password');
        $baseUrl = config('services.esolutions.base_url');
        $sender = config('services.esolutions.sender');
        $credentials = base64_encode("{$username}:{$password}");

        $sent = 0;
        $failed = 0;

        // Fire requests in concurrent batches of 50.
        foreach (array_chunk($messages, 50) as $chunk) {
            $chunk = array_values($chunk);

            $responses = Http::pool(function ($pool) use ($chunk, $credentials, $baseUrl, $sender) {
                $requests = [];
                foreach ($chunk as $i => $item) {
                    $payload = [
                        'originator' => $sender,
                        'destination' => $item['phone'],
                        'messageText' => $item['message'],
                        'messageReference' => Str::random(10),
                        'messageDate' => date('YmdHis'),
                        'messageValidity' => date('H:i:s', strtotime('+3 hours')),
                        'sendDateTime' => date('H:i:s'),
                    ];

                    $requests[] = $pool->as((string) $i)
                        ->withHeaders([
                            'Authorization' => "Basic {$credentials}",
                            'Content-Type' => 'application/json',
                            'Accept' => 'application/json',
                        ])
                        ->withBody(json_encode($payload), 'application/json')
                        ->post($baseUrl.'/single');
                }

                return $requests;
            });

            foreach ($chunk as $i => $item) {
                $response = $responses[(string) $i] ?? null;
                try {
                    if ($response
                        && ! $response instanceof \Throwable
                        && $response->successful()
                        && isset($response->json()['messageId'])) {
                        $sent++;
                    } else {
                        $failed++;
                    }
                } catch (\Throwable $e) {
                    $failed++;
                    Log::error('Batch SMS failed for '.$item['phone'].': '.$e->getMessage());
                }
            }
        }

        return ['sent' => $sent, 'failed' => $failed];
    }

    private function sendViaTwilio($phone, $message)
    {
        $accountSid = config('services.twilio.account_sid');
        $authToken = config('services.twilio.auth_token');
        $fromNumber = config('services.twilio.from_number');


        if (! $accountSid || ! $authToken) {
            throw new \Exception('Twilio credentials not configured');
        }

        // Send via Twilio API
        $response = Http::withBasicAuth($accountSid, $authToken)
            ->asForm()
            ->post("https://api.twilio.com/2010-04-01/Accounts/{$accountSid}/Messages.json", [
                'From' => $fromNumber,
                'To' => $phone,
                'Body' => $message,
            ]);

        if (! $response->successful()) {
            throw new \Exception('Twilio error: '.$response->body());
        }

        return $response->json();
    }

    private function sendViaAfricasTalking($phone, $message)
    {
        $username = config('services.africastalking.username');
        $apiKey = config('services.africastalking.api_key');
        $from = config('services.africastalking.from');

        if (! $username || ! $apiKey) {
            throw new \Exception('Africa\'s Talking credentials not configured');
        }

        $response = Http::withHeaders([
            'apiKey' => $apiKey,
            'Content-Type' => 'application/x-www-form-urlencoded',
            'Accept' => 'application/json',
        ])->asForm()->post('https://api.africastalking.com/version1/messaging', [
            'username' => $username,
            'to' => $phone,
            'message' => $message,
            'from' => $from,
        ]);

        if (! $response->successful()) {
            throw new \Exception('Africa\'s Talking error: '.$response->body());
        }

        return $response->json();
    }


    private function sendViaESolutions($phone, $message)
    {
        $username = config('services.esolutions.username');
        $password = config('services.esolutions.password');
        $base_url = config('services.esolutions.base_url');
        $sender = config('services.esolutions.sender');
        $payload = [
            "originator" => $sender,
            "destination" => $phone,
            "messageText" => $message,
            "messageReference" => Str::random(10),
            "messageDate" => date('YmdHis'),
            "messageValidity" => date('H:i:s', strtotime('+3 hours')),
            "sendDateTime" => date('H:i:s')
        ];

        // Manually set Basic Auth header to match C# implementation
        // C# equivalent: Convert.ToBase64String(Encoding.ASCII.GetBytes($"{username}:{password}"))
      
        $credentials = base64_encode("{$username}:{$password}");

        try {
            $response = Http::withHeaders([
                'Authorization' => "Basic {$credentials}",
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])
                ->withBody(json_encode($payload), 'application/json')
                ->post($base_url.'/single');

           Log::error($response);

            // Check if request was successful
            if ($response->successful()) {
                $responseData = $response->json();

                // Check if response contains messageId (indicates success)
                if (isset($responseData['messageId'])) {
                   return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } catch (\Exception $e) {
         Log::error($e);
         return false;
        }
    }

    public function getCampaignStatistics($campaignId)
    {
        $campaign = $this->getCampaignById($campaignId);

        return [
            'total_recipients' => $campaign->total_recipients,
            'sent' => $campaign->sent_count,
            'failed' => $campaign->failed_count,
            'pending' => $campaign->pending_count,
            'progress_percentage' => $campaign->progress_percentage,
            'credits_used' => $campaign->credits_used,
        ];
    }

    public function deleteCampaign($campaignId)
    {
        try {
            $campaign = $this->smsbroadcast->find($campaignId);
            if (! $campaign) {
                return ['status' => 'error', 'message' => 'Campaign not found'];
            }
            $campaign->recipients()->delete();
            $campaign->delete();
            return ['status' => 'success', 'message' => 'Campaign deleted successfully'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    /**
     * Poll Nhume API for delivery status of each recipient that has a
     * provider_message_id. Update recipient rows and campaign status.
     */
    public function checkDeliveryStatus($campaignId): array
    {
        $campaign = $this->getCampaignById($campaignId);

        if ($campaign->provider !== 'nhume') {
            return ['status' => 'error', 'message' => 'Delivery polling is only supported for Nhume campaigns.'];
        }

        $apiKey     = config('services.nhume.api_key');
        $recipients = $campaign->recipients()
            ->whereNotNull('provider_message_id')
            ->whereNotIn('status', ['DELIVERED', 'FAILED'])
            ->get();

        $updated = 0;

        foreach ($recipients as $recipient) {
            try {
                $response = Http::withHeaders([
                    'Authorization' => "Bearer {$apiKey}",
                    'Accept'        => 'application/json',
                ])->get('https://api.nhume.co.zw/api/v1/sms/messages/' . $recipient->provider_message_id);

                if ($response->successful()) {
                    $data   = $response->json('data');
                    $status = strtoupper($data['status'] ?? '');

                    if (in_array($status, ['DELIVERED', 'SENT', 'FAILED', 'UNDELIVERED'])) {
                        $recipient->update([
                            'status'       => in_array($status, ['DELIVERED', 'SENT']) ? 'SENT' : 'FAILED',
                            'delivered_at' => $data['delivered_at'] ?? null,
                        ]);
                        $updated++;
                    }
                }
            } catch (\Exception $e) {
                Log::error('Nhume status check failed for '.$recipient->provider_message_id.': '.$e->getMessage());
            }
        }

        // Refresh counts
        $campaign->refresh();
        $totalSent   = $campaign->recipients()->where('status', 'SENT')->count();
        $totalFailed = $campaign->recipients()->where('status', 'FAILED')->count();
        $totalPending = $campaign->recipients()->whereNotIn('status', ['SENT', 'FAILED'])->count();

        $newStatus = $totalPending === 0 ? 'SENT' : $campaign->status;

        $campaign->update([
            'sent_count'   => $totalSent,
            'failed_count' => $totalFailed,
            'credits_used' => $totalSent,
            'status'       => $newStatus,
        ]);

        return [
            'status'  => 'success',
            'message' => "Checked {$recipients->count()} messages. Updated {$updated}. Campaign: {$newStatus}.",
            'pending' => $totalPending,
        ];
    }
}




