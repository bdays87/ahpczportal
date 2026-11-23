<?php

namespace App\Services\Whatsapp;

use App\Models\WhatsappMessage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Thin client for Meta's WhatsApp Business Cloud API: sends text, buttons, lists and documents.
 */
class WhatsappClient
{
    private function base(): string
    {
        return 'https://graph.facebook.com/'.config('services.whatsapp.api_version').'/'.config('services.whatsapp.phone_number_id');
    }

    private function http()
    {
        return Http::withToken((string) config('services.whatsapp.token'))->timeout(60);
    }

    public function sendText(string $to, string $text): bool
    {
        return $this->send($to, [
            'type' => 'text',
            'text' => ['body' => mb_substr($text, 0, 4000), 'preview_url' => false],
        ], $text);
    }

    /** Up to 3 quick-reply buttons. $buttons = [['id' => 'x', 'title' => 'Label (max 20)']]. */
    public function sendButtons(string $to, string $body, array $buttons): bool
    {
        return $this->send($to, [
            'type' => 'interactive',
            'interactive' => [
                'type' => 'button',
                'body' => ['text' => mb_substr($body, 0, 1000)],
                'action' => ['buttons' => array_map(fn ($b) => [
                    'type' => 'reply',
                    'reply' => ['id' => $b['id'], 'title' => mb_substr($b['title'], 0, 20)],
                ], array_slice($buttons, 0, 3))],
            ],
        ], $body);
    }

    /** A pick-list (up to 10 rows). $rows = [['id' => 'x', 'title' => 'max 24', 'description' => 'max 72']]. */
    public function sendList(string $to, string $body, string $buttonLabel, array $rows, string $sectionTitle = 'Options'): bool
    {
        return $this->send($to, [
            'type' => 'interactive',
            'interactive' => [
                'type' => 'list',
                'body' => ['text' => mb_substr($body, 0, 1000)],
                'action' => [
                    'button' => mb_substr($buttonLabel, 0, 20),
                    'sections' => [[
                        'title' => mb_substr($sectionTitle, 0, 24),
                        'rows' => array_map(fn ($r) => array_filter([
                            'id' => $r['id'],
                            'title' => mb_substr($r['title'], 0, 24),
                            'description' => isset($r['description']) ? mb_substr($r['description'], 0, 72) : null,
                        ]), array_slice($rows, 0, 10)),
                    ]],
                ],
            ],
        ], $body);
    }

    /** Upload a PDF to WhatsApp and send it to the user as a document. */
    public function sendDocument(string $to, string $binary, string $filename, string $caption = ''): bool
    {
        try {
            $upload = $this->http()->attach('file', $binary, $filename, ['Content-Type' => 'application/pdf'])
                ->post($this->base().'/media', ['messaging_product' => 'whatsapp', 'type' => 'application/pdf']);

            if (! $upload->successful() || ! $upload->json('id')) {
                Log::error('WhatsApp media upload failed', ['status' => $upload->status(), 'body' => $upload->body()]);

                return false;
            }

            return $this->send($to, [
                'type' => 'document',
                'document' => array_filter(['id' => $upload->json('id'), 'filename' => $filename, 'caption' => $caption ?: null]),
            ], '[document] '.$filename);
        } catch (\Throwable $e) {
            Log::error('WhatsApp document send failed: '.$e->getMessage());

            return false;
        }
    }

    /**
     * Download a file the user sent (photo or document). Returns ['bytes' => ..., 'mime' => ...] or null.
     */
    public function downloadMedia(string $mediaId): ?array
    {
        try {
            $meta = Http::withToken((string) config('services.whatsapp.token'))->timeout(30)
                ->get('https://graph.facebook.com/'.config('services.whatsapp.api_version').'/'.$mediaId);
            if (! $meta->successful() || ! $meta->json('url')) {
                Log::error('WhatsApp media lookup failed', ['status' => $meta->status(), 'body' => $meta->body()]);

                return null;
            }

            $file = Http::withToken((string) config('services.whatsapp.token'))->timeout(120)->get($meta->json('url'));
            if (! $file->successful()) {
                Log::error('WhatsApp media download failed', ['status' => $file->status()]);

                return null;
            }

            return ['bytes' => $file->body(), 'mime' => (string) ($meta->json('mime_type') ?: $file->header('Content-Type'))];
        } catch (\Throwable $e) {
            Log::error('WhatsApp media download error: '.$e->getMessage());

            return null;
        }
    }

    /** A pre-approved template message (needed to message someone first, outside the 24-hour window). */
    public function sendTemplate(string $to, string $template, array $bodyParams = [], string $language = 'en'): bool
    {
        $components = [];
        if (! empty($bodyParams)) {
            $components[] = ['type' => 'body', 'parameters' => array_map(fn ($p) => ['type' => 'text', 'text' => mb_substr((string) $p, 0, 1000)], $bodyParams)];
        }

        return $this->send($to, [
            'type' => 'template',
            'template' => ['name' => $template, 'language' => ['code' => $language], 'components' => $components],
        ], '[template] '.$template);
    }

    private function send(string $to, array $payload, string $logBody): bool
    {
        try {
            $response = $this->http()->post($this->base().'/messages', array_merge([
                'messaging_product' => 'whatsapp',
                'recipient_type' => 'individual',
                'to' => $to,
            ], $payload));

            if (! $response->successful()) {
                Log::error('WhatsApp send failed', ['status' => $response->status(), 'body' => $response->body()]);

                return false;
            }

            // The message is already sent. Keeping a log of it must never turn that into a failure.
            try {
                WhatsappMessage::create([
                    'wa_message_id' => $response->json('messages.0.id'),
                    'phone' => $to,
                    'direction' => 'out',
                    'type' => $payload['type'],
                    'body' => mb_substr($logBody, 0, 1000),
                ]);
            } catch (\Throwable $e) {
                Log::warning('WhatsApp message log failed: '.$e->getMessage());
            }

            return true;
        } catch (\Throwable $e) {
            Log::error('WhatsApp send error: '.$e->getMessage());

            return false;
        }
    }
}
