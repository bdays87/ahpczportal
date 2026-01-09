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
        $data['status'] = 'DRAFT';

        $campaign = $this->smsbroadcast->create($data);

        // Get recipients based on filters
        $recipients = $this->getFilteredRecipients($data['filters'] ?? []);

        // Create recipient records
        foreach ($recipients as $customer) {
            if ($customer->phone) {
                $this->recipient->create([
                    'smsbroadcast_id' => $campaign->id,
                    'customer_id' => $customer->id,
                    'phone' => $customer->phone,
                    'status' => 'PENDING',
                ]);
            }
        }

        // Update total recipients count
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
                // Send SMS via gateway
                $result = $this->sendSMS($recipient->phone, $campaign->message);
                if ($result) {
                    $recipient->update([
                        'status' => 'SENT',
                        'sent_at' => now(),
                    ]);
                    $sentCount++;
                } else {
                    $recipient->update([
                        'status' => 'FAILED',
                        'error_message' => 'Failed to send SMS',
                    ]);
                    $failedCount++;
                }
            } catch (\Exception $e) {
                $failedCount++;
            }
        }

        // Update campaign statistics
        $campaign->update([
            'sent_count' => $campaign->sent_count + $sentCount,
            'failed_count' => $campaign->failed_count + $failedCount,
            'credits_used' => $campaign->sent_count + $sentCount,
            'status' => ($campaign->pending_count === 0) ? 'SENT' : 'SENDING',
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
        return $this->sendViaESolutions($phone, $message);
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
}




