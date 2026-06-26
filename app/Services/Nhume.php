<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

/**
 * Thin client for the Nhume email API (https://api.nhume.co.zw/api/v1).
 *
 * Auth: API key sent as a Bearer token.
 * Endpoints used: POST /send, POST /send/bulk, GET /usage, GET /messages/{id}.
 */
class Nhume
{
    protected string $baseUrl;

    protected ?string $apiKey;

    protected ?string $from;

    protected ?string $fromName;

    public function __construct()
    {
        $this->baseUrl = rtrim((string) config('services.nhume.base_url', 'https://api.nhume.co.zw/api/v1'), '/');
        $this->apiKey = config('services.nhume.api_key');
        $this->from = config('services.nhume.from');
        $this->fromName = config('services.nhume.from_name');
    }

    /** Whether Nhume has the minimum configuration to send. */
    public function configured(): bool
    {
        return ! empty($this->apiKey) && ! empty($this->from);
    }

    protected function http()
    {
        return Http::withToken($this->apiKey)
            ->acceptJson()
            ->timeout(30)
            ->baseUrl($this->baseUrl);
    }

    /**
     * Send a single transactional email. Returns the message data on success.
     */
    public function send(string $to, string $subject, string $html, ?string $toName = null, ?string $text = null): array
    {
        $payload = array_filter([
            'from' => $this->from,
            'from_name' => $this->fromName,
            'to' => $to,
            'to_name' => $toName,
            'subject' => $subject,
            'html' => $html,
            'text' => $text,
        ], fn ($v) => $v !== null && $v !== '');

        $response = $this->http()->post('/send', $payload);

        if (! $response->successful()) {
            throw new \Exception('Nhume send error ['.$response->status().']: '.$response->body());
        }

        return $response->json('data') ?? [];
    }

    /**
     * Send the same email to many recipients (up to 1,000) in one request.
     *
     * @param  array  $recipients  Array of ['to' => string, 'to_name' => string|null].
     */
    public function sendBulk(array $recipients, string $subject, string $html, ?string $text = null): array
    {
        $payload = array_filter([
            'from' => $this->from,
            'subject' => $subject,
            'html' => $html,
            'text' => $text,
            'recipients' => array_values($recipients),
        ], fn ($v) => $v !== null && $v !== '');

        $response = $this->http()->post('/send/bulk', $payload);

        if (! $response->successful()) {
            throw new \Exception('Nhume bulk error ['.$response->status().']: '.$response->body());
        }

        return $response->json() ?? [];
    }

    /**
     * Current plan, billing period and per-type credit consumption.
     */
    public function usage(): array
    {
        $response = $this->http()->get('/usage');

        if (! $response->successful()) {
            throw new \Exception('Nhume usage error ['.$response->status().']: '.$response->body());
        }

        return $response->json('data') ?? [];
    }

    /**
     * Remaining credits for a given credit type (defaults to TRANSACTIONAL).
     * Returns null if the type is not present.
     */
    public function remainingCredits(string $type = 'TRANSACTIONAL'): ?int
    {
        $usage = $this->usage();
        foreach (($usage['credits'] ?? []) as $credit) {
            if (($credit['type'] ?? '') === $type) {
                return isset($credit['remaining']) ? (int) $credit['remaining'] : null;
            }
        }

        return null;
    }

    /**
     * Fetch the delivery status of a previously sent message.
     */
    public function getMessage(string $id): array
    {
        $response = $this->http()->get('/messages/'.$id);

        if (! $response->successful()) {
            throw new \Exception('Nhume message error ['.$response->status().']: '.$response->body());
        }

        return $response->json('data') ?? [];
    }
}
