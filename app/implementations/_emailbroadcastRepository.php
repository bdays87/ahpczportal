<?php

namespace App\implementations;

use App\Interfaces\iemailbroadcastInterface;
use App\Models\Customer;
use App\Models\Emailbroadcast;
use App\Models\Emailbroadcastrecipient;
use App\Models\Emailcredit;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class _emailbroadcastRepository implements iemailbroadcastInterface
{
    protected $emailcredit;

    protected $emailbroadcast;

    protected $recipient;

    protected $customer;

    public function __construct(
        Emailcredit $emailcredit,
        Emailbroadcast $emailbroadcast,
        Emailbroadcastrecipient $recipient,
        Customer $customer
    ) {
        $this->emailcredit = $emailcredit;
        $this->emailbroadcast = $emailbroadcast;
        $this->recipient = $recipient;
        $this->customer = $customer;
    }

    public function addCredits(array $data)
    {
        $data['addedby'] = Auth::id();
        $data['used_credits'] = 0;
        $data['remaining_credits'] = $data['credits'];

        return $this->emailcredit->create($data);
    }

    public function getTotalCredits()
    {
        return $this->emailcredit->sum('credits');
    }

    public function getUsedCredits()
    {
        return $this->emailbroadcast->sum('credits_used');
    }

    public function getRemainingCredits()
    {
        return $this->getTotalCredits() - $this->getUsedCredits();
    }

    public function getCreditHistory()
    {
        return $this->emailcredit->with('addedBy')->orderBy('created_at', 'desc')->get();
    }

    public function createCampaign(array $data)
    {
        $data['createdby'] = Auth::id();
        $data['status'] = 'DRAFT';

        $campaign = $this->emailbroadcast->create($data);

        // Get recipients based on filters
        $recipients = $this->getFilteredRecipients($data['filters'] ?? []);

        // Create recipient records
        foreach ($recipients as $customer) {
            $this->recipient->create([
                'emailbroadcast_id' => $campaign->id,
                'customer_id' => $customer->id,
                'email' => $customer->email,
                'status' => 'PENDING',
            ]);
        }

        // Update total recipients count
        $campaign->update(['total_recipients' => $recipients->count()]);

        return $campaign;
    }

    public function getCampaigns()
    {
        return $this->emailbroadcast
            ->with('creator', 'recipients')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getCampaignById($id)
    {
        return $this->emailbroadcast
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

        return $query->whereNotNull('email')->get();
    }

    public function sendBroadcast($campaignId)
    {
        $campaign = $this->getCampaignById($campaignId);

        // Check if enough credits
        $remainingCredits = $this->getRemainingCredits();
        if ($remainingCredits < $campaign->pending_count) {
            return [
                'status' => 'error',
                'message' => 'Insufficient email credits. Need '.$campaign->pending_count.' credits, have '.$remainingCredits,
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
                // Send via SendGrid
                $this->sendViaSendGrid($recipient->email, $campaign->subject, $campaign->message, $campaign->attachments);

                // Update recipient status
                $recipient->update([
                    'status' => 'SENT',
                    'sent_at' => now(),
                ]);

                $sentCount++;
            } catch (\Exception $e) {
                // Update recipient status
                $recipient->update([
                    'status' => 'FAILED',
                    'error_message' => $e->getMessage(),
                ]);

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
            'message' => "Sent {$sentCount} emails, {$failedCount} failed",
            'sent' => $sentCount,
            'failed' => $failedCount,
        ];
    }

    private function sendViaSendGrid($to, $subject, $message, $attachments = null)
    {
        // Check if SendGrid package is installed and configured
        if (class_exists('\SendGrid') && config('services.sendgrid.api_key')) {
            return $this->sendWithSendGridAPI($to, $subject, $message, $attachments);
        }

        // Fallback to Laravel Mail (works with any configured mail driver)
        return $this->sendWithLaravelMail($to, $subject, $message, $attachments);
    }

    private function sendWithSendGridAPI($to, $subject, $message, $attachments)
    {
        $apiKey = config('services.sendgrid.api_key');

        $email = new \SendGrid\Mail\Mail;
        $email->setFrom(config('mail.from.address'), config('mail.from.name'));
        $email->setSubject($subject);
        $email->addTo($to);
        $email->addContent('text/html', $message);

        // Add attachments if provided
        if ($attachments && is_array($attachments)) {
            foreach ($attachments as $attachment) {
                if (file_exists(storage_path('app/public/'.$attachment))) {
                    $fileContent = base64_encode(file_get_contents(storage_path('app/public/'.$attachment)));
                    $email->addAttachment(
                        $fileContent,
                        'application/octet-stream',
                        basename($attachment),
                        'attachment'
                    );
                }
            }
        }

        $sendgrid = new \SendGrid($apiKey);
        $response = $sendgrid->send($email);

        if ($response->statusCode() >= 400) {
            throw new \Exception('SendGrid error: '.$response->body());
        }

        return $response;
    }

    private function sendWithLaravelMail($to, $subject, $message, $attachments)
    {
        Mail::send([], [], function ($mail) use ($to, $subject, $message, $attachments) {
            $mail->to($to)
                ->subject($subject)
                ->html($message);

            // Add attachments if provided
            if ($attachments && is_array($attachments)) {
                foreach ($attachments as $attachment) {
                    $filePath = storage_path('app/public/'.$attachment);
                    if (file_exists($filePath)) {
                        $mail->attach($filePath);
                    }
                }
            }
        });

        return true;
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
