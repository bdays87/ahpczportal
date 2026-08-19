<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

/**
 * Thin client for the local SageBridge service (see
 * sageintegration/SageBridge), which is what actually talks to the Sage
 * Evolution SDK. This class only knows HTTP — it has no knowledge of Sage's
 * object model.
 *
 * Auth: shared secret sent as the X-Api-Key header (config('sage.api_key')).
 * Endpoints used: GET /api/health, POST /api/customers, GET /api/customers/{code},
 * POST /api/invoices, POST /api/receipts.
 */
class SageClient
{
    protected string $baseUrl;

    protected ?string $apiKey;

    protected int $timeout;

    public function __construct()
    {
        $this->baseUrl = rtrim((string) config('sage.base_url', 'http://127.0.0.1:8990'), '/');
        $this->apiKey = config('sage.api_key');
        $this->timeout = (int) config('sage.timeout', 15);
    }

    /** Whether the integration is turned on and minimally configured. */
    public function configured(): bool
    {
        return (bool) config('sage.enabled') && ! empty($this->apiKey);
    }

    protected function http(): PendingRequest
    {
        return Http::withHeaders(['X-Api-Key' => $this->apiKey])
            ->acceptJson()
            ->timeout($this->timeout)
            ->baseUrl($this->baseUrl);
    }

    /** GET /api/health — connectivity + license check. */
    public function health(): array
    {
        return $this->http()->get('/api/health')->throw()->json();
    }

    /**
     * POST /api/customers — create-or-update by code. Safe to call repeatedly
     * for the same customer.
     *
     * @param  array{code:string,description:string,email?:string,telephone?:string,taxNumber?:string,addressLine1?:string,addressLine2?:string,addressLine3?:string,addressLine4?:string}  $payload
     */
    public function upsertCustomer(array $payload): array
    {
        return $this->post('/api/customers', $payload);
    }

    /**
     * POST /api/invoices.
     *
     * @param  array{customerCode:string,reference:string,invoiceDate:string,lines:array<int,array{glAccountCode:string,description:string,quantity:float,unitPrice:float}>}  $payload
     */
    public function createInvoice(array $payload): array
    {
        return $this->post('/api/invoices', $payload);
    }

    /**
     * POST /api/receipts.
     *
     * @param  array{customerCode:string,invoiceReference:string,reference:string,amount:float,receiptDate:string}  $payload
     */
    public function createReceipt(array $payload): array
    {
        return $this->post('/api/receipts', $payload);
    }

    /**
     * @throws RequestException on a non-2xx response, with the bridge's
     *                          {"error": "..."} message attached.
     */
    protected function post(string $path, array $payload): array
    {
        return $this->http()->post($path, $payload)->throw()->json();
    }
}
