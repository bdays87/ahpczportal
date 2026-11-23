<?php

namespace App\Services\Whatsapp;

use App\Models\Customer;
use App\Models\WhatsappLink;
use App\Models\WhatsappSession;
use Illuminate\Support\Facades\Log;

/**
 * Tells a practitioner on WhatsApp when something changes (approved, rejected, payment verified).
 *
 * WhatsApp only lets us write freely within 24 hours of the practitioner's last message. After that a
 * message must use a template Meta has approved. Set WHATSAPP_STATUS_TEMPLATE to the name of an approved
 * template with a single {{1}} variable; without it, late messages are skipped (and logged).
 */
class WhatsappNotifier
{
    public function __construct(private readonly WhatsappClient $wa) {}

    public function notify(Customer $customer, string $text): void
    {
        if (! config('services.whatsapp.enabled')) {
            return;
        }

        foreach (WhatsappLink::where('customer_id', $customer->id)->get() as $link) {
            try {
                $session = WhatsappSession::where('phone', $link->phone)->first();
                $inWindow = $session && $session->last_message_at && $session->last_message_at->gt(now()->subHours(24));
                $template = config('services.whatsapp.status_template');

                if ($inWindow) {
                    $this->wa->sendText($link->phone, $text);
                } elseif ($template) {
                    $this->wa->sendTemplate($link->phone, $template, [$text]);
                } else {
                    Log::info('WhatsApp update not sent: more than 24 hours since the last message and no WHATSAPP_STATUS_TEMPLATE set', ['customer_id' => $customer->id]);
                }
            } catch (\Throwable $e) {
                Log::error('WhatsApp notification failed: '.$e->getMessage());
            }
        }
    }
}
