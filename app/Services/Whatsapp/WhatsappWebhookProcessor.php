<?php

namespace App\Services\Whatsapp;

use App\Models\WhatsappMessage;
use App\Models\WhatsappSession;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

/**
 * Turns a Meta webhook payload into plain messages and hands each to the bot.
 * Meta sometimes delivers the same message twice, so each message id is only handled once.
 */
class WhatsappWebhookProcessor
{
    public function __construct(private readonly WhatsappBot $bot) {}

    public function process(array $payload): int
    {
        $handled = 0;

        foreach ($payload['entry'] ?? [] as $entry) {
            foreach ($entry['changes'] ?? [] as $change) {
                foreach ($change['value']['messages'] ?? [] as $message) {
                    if ($this->handleOne($message)) {
                        $handled++;
                    }
                }
            }
        }

        return $handled;
    }

    private function handleOne(array $message): bool
    {
        $id = $message['id'] ?? null;
        $from = $message['from'] ?? null;
        if (! $id || ! $from) {
            return false;
        }

        $type = $message['type'] ?? 'text';
        $text = $message['text']['body'] ?? '';
        $replyId = $message['interactive']['button_reply']['id'] ?? $message['interactive']['list_reply']['id'] ?? null;

        // Never keep what the user typed while we are waiting for their verification code
        $session = WhatsappSession::where('phone', preg_replace('/\D/', '', $from))->first();
        $body = ($session && $session->state === 'await_code') ? '[hidden]' : ($replyId ?? $text);

        try {
            WhatsappMessage::create([
                'wa_message_id' => $id,
                'phone' => preg_replace('/\D/', '', $from),
                'direction' => 'in',
                'type' => $type,
                'body' => mb_substr((string) $body, 0, 1000),
            ]);
        } catch (QueryException $e) {
            return false; // already handled
        }

        try {
            $this->bot->handle(['from' => $from, 'id' => $id, 'type' => $type, 'text' => $text, 'reply_id' => $replyId]);
        } catch (\Throwable $e) {
            Log::error('WhatsApp webhook handling failed: '.$e->getMessage());
        }

        return true;
    }
}
