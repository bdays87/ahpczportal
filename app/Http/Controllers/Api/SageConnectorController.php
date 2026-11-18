<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customerprofession;
use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

/**
 * Portal side of the Sage connector contract.
 *
 * The connector (on the LAN next to Sage Evolution) polls these endpoints, pushes
 * the data to the Chips Sage Integration API and reports back.
 *
 * Sage customer code = the profession's registration number
 * (customerprofessions.registrationnumber). Sync status is tracked per customer
 * profession, so a practitioner with two professions is two Sage customers.
 */
class SageConnectorController extends Controller
{
    /**
     * Find the customer profession a Sage customer code refers to. The code is the
     * registration number; sage_customer_code is checked first because it is what
     * Sage acknowledged.
     */
    private function findProfessionByCode(string $code): ?Customerprofession
    {
        return Customerprofession::where('sage_customer_code', $code)->first()
            ?? Customerprofession::where('registrationnumber', $code)->first();
    }

    /**
     * Optional ?codes=A,B restricts a sync to specific customer codes (registration numbers).
     * Used to test the integration with one or two practitioners before opening it up.
     */
    private function codes(Request $request): ?array
    {
        $codes = array_values(array_filter(array_map('trim', explode(',', (string) $request->input('codes', '')))));

        return $codes ?: null;
    }

    /**
     * GET /api/connector/customers/pending
     *
     * Customer professions awaiting sync to Sage. Query: since (datetime, default 7 days
     * ago), limit. Only APPROVED professions that have an APPROVED registration and
     * application, a registration number, and are not yet synced (or failed before).
     */
    public function getPendingCustomers(Request $request)
    {
        try {
            $sinceDate = Carbon::parse($request->input('since', Carbon::now()->subDays(7)->toIso8601String()));
            $limit = min(max((int) $request->input('limit', 100), 1), 500);

            $customers = Customerprofession::query()
                ->with('customer')
                ->where('status', 'APPROVED')
                ->where('updated_at', '>=', $sinceDate)
                ->whereNotNull('registrationnumber')
                ->where('registrationnumber', '!=', '')
                ->when($this->codes($request), fn ($q, $codes) => $q->whereIn('registrationnumber', $codes))
                ->whereHas('applications', fn ($q) => $q->where('status', 'APPROVED'))
                ->whereHas('registration', fn ($q) => $q->where('status', 'APPROVED'))
                ->where(function ($q) {
                    $q->whereNull('sage_sync_status')
                        ->orWhereIn('sage_sync_status', ['PENDING', 'FAILED']);
                })
                ->orderBy('id')
                ->limit($limit)
                ->get()
                ->filter(fn ($profession) => $profession->customer)
                ->map(function ($profession) {
                    $customer = $profession->customer;
                    $fullName = trim($customer->name.' '.$customer->surname);

                    return [
                        'externalId' => trim($profession->registrationnumber),
                        'name' => $fullName,
                        'contact' => $fullName,
                        'email' => $customer->email,
                        'phone' => $customer->phone,
                        'vatNumber' => $customer->vat_number ?? null,
                        'physicalAddress' => $customer->address,
                        'createdUtc' => ($profession->updated_at ?? $customer->created_at)->toIso8601String(),
                    ];
                })
                ->values();

            return response()->json($customers);
        } catch (\Exception $e) {
            Log::error('SageConnector: getPendingCustomers failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    /**
     * GET /api/connector/orders/pending
     *
     * PAID invoices awaiting creation in Sage, for customers already synced to Sage.
     * Query: since (datetime, default 7 days ago), limit.
     */
    public function getPendingOrders(Request $request)
    {
        try {
            $sinceDate = Carbon::parse($request->input('since', Carbon::now()->subDays(7)->toIso8601String()));
            $limit = min(max((int) $request->input('limit', 100), 1), 500);

            $invoices = Invoice::query()
                ->where('status', 'PAID')
                ->where('created_at', '>=', $sinceDate)
                ->whereHas('customer.customerprofessions', function ($query) use ($request) {
                    $query->where('sage_sync_status', 'SYNCED')
                        ->whereNotNull('sage_customer_code')
                        ->when($this->codes($request), fn ($q, $codes) => $q->whereIn('sage_customer_code', $codes));
                })
                ->where(function ($query) {
                    $query->whereNull('sage_sync_status')
                        ->orWhereIn('sage_sync_status', ['PENDING', 'FAILED']);
                })
                ->orderBy('id')
                ->limit($limit)
                ->get()
                ->map(function ($invoice) {
                    // The Sage customer code of the invoice's customer.
                    $profession = Customerprofession::where('customer_id', $invoice->customer_id)
                        ->where('sage_sync_status', 'SYNCED')
                        ->whereNotNull('sage_customer_code')
                        ->orderBy('id')
                        ->first();

                    if (! $profession) {
                        return null;
                    }

                    $description = $invoice->description ?: 'Service Fee';

                    return [
                        'externalId' => $invoice->invoice_number,
                        'customerExternalId' => $profession->sage_customer_code,
                        'sageCustomerCode' => $profession->sage_customer_code,
                        'orderDate' => $invoice->created_at->toIso8601String(),
                        'reference' => $invoice->invoice_number,
                        'source' => $invoice->source,
                        'description' => $description,
                        'lines' => [[
                            'stockCode' => 'SERVICE-'.strtoupper(trim(preg_replace('/[^A-Za-z0-9]+/', '-', $description), '-')),
                            'description' => $description,
                            'quantity' => 1,
                            'unitPrice' => (float) $invoice->amount,
                        ]],
                    ];
                })
                ->filter()
                ->values();

            return response()->json($invoices);
        } catch (\Exception $e) {
            Log::error('SageConnector: getPendingOrders failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    /**
     * POST /api/connector/ack
     *
     * Receives acknowledgment of sync success/failure from Sage.
     */
    public function receiveAck(Request $request)
    {
        try {
            $validated = $request->validate([
                'externalId' => 'required|string',
                'kind' => 'required|string|in:customer,order',
                'success' => 'required|boolean',
                'sageReference' => 'nullable|string',
                'errorMessage' => 'nullable|string',
                'processedUtc' => 'required|date',
            ]);

            if ($validated['kind'] === 'customer') {
                $profession = $this->findProfessionByCode($validated['externalId']);

                if ($profession) {
                    $profession->update([
                        'sage_sync_status' => $validated['success'] ? 'SYNCED' : 'FAILED',
                        'sage_customer_code' => $validated['sageReference'] ?? $validated['externalId'],
                        'sage_sync_error' => $validated['errorMessage'] ?? null,
                        'sage_synced_at' => Carbon::parse($validated['processedUtc']),
                    ]);
                } else {
                    Log::warning('SageConnector: Customer not found for ack', ['externalId' => $validated['externalId']]);
                }
            } else {
                $invoice = Invoice::where('invoice_number', $validated['externalId'])->first();

                if ($invoice) {
                    $invoice->update([
                        'sage_sync_status' => $validated['success'] ? 'SYNCED' : 'FAILED',
                        'sage_invoice_number' => $validated['sageReference'] ?? $invoice->sage_invoice_number,
                        'sage_sync_error' => $validated['errorMessage'] ?? null,
                        'sage_synced_at' => Carbon::parse($validated['processedUtc']),
                    ]);
                } else {
                    Log::warning('SageConnector: Invoice not found for ack', ['externalId' => $validated['externalId']]);
                }
            }

            return response()->json(['message' => 'Acknowledgment received'], 200);
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('SageConnector: receiveAck failed', ['error' => $e->getMessage(), 'request' => $request->all()]);

            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    /**
     * POST /api/connector/invoices
     *
     * Receives an invoice snapshot read from Sage. Matched to a portal invoice by the
     * Sage invoice number, or by externalId (the portal invoice number we supplied).
     * A Sage invoice for a customer that is not in the portal gets a 404 and is skipped.
     */
    public function receiveInvoice(Request $request)
    {
        try {
            $validated = $request->validate([
                'invoiceNumber' => 'required|string',
                'customerCode' => 'required|string',
                'invoiceDate' => 'required|date',
                'totalExclTax' => 'required|numeric',
                'totalTax' => 'required|numeric',
                'totalInclTax' => 'required|numeric',
                'amountOutstanding' => 'required|numeric',
                'status' => 'required|string',
                'externalId' => 'nullable|string',
            ]);

            $profession = $this->findProfessionByCode($validated['customerCode']);

            if (! $profession) {
                return response()->json(['error' => 'Customer not found'], 404);
            }

            $invoice = Invoice::where('sage_invoice_number', $validated['invoiceNumber'])->first();
            if (! $invoice && ! empty($validated['externalId'])) {
                $invoice = Invoice::where('invoice_number', $validated['externalId'])
                    ->where('customer_id', $profession->customer_id)
                    ->first();
            }

            if (! $invoice) {
                // A Sage invoice for this customer that did not originate in the portal.
                return response()->json(['message' => 'Invoice not tracked in portal'], 200);
            }

            $invoice->update([
                'sage_invoice_number' => $validated['invoiceNumber'],
                'sage_total_excl_tax' => $validated['totalExclTax'],
                'sage_total_tax' => $validated['totalTax'],
                'sage_total_incl_tax' => $validated['totalInclTax'],
                'sage_outstanding' => $validated['amountOutstanding'],
                'sage_status' => $validated['status'],
                'sage_invoice_date' => Carbon::parse($validated['invoiceDate']),
                'sage_last_updated_at' => now(),
            ]);

            return response()->json(['message' => 'Invoice received'], 200);
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('SageConnector: receiveInvoice failed', ['error' => $e->getMessage(), 'request' => $request->all()]);

            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    /**
     * POST /api/connector/statements
     *
     * Receives customer balance / ageing data from Sage.
     */
    public function receiveStatement(Request $request)
    {
        try {
            $validated = $request->validate([
                'customerCode' => 'required|string',
                'currentBalance' => 'required|numeric',
                'current' => 'required|numeric',
                'days30' => 'required|numeric',
                'days60' => 'required|numeric',
                'days90Plus' => 'required|numeric',
                'asOfUtc' => 'required|date',
            ]);

            $profession = $this->findProfessionByCode($validated['customerCode']);

            if (! $profession) {
                return response()->json(['error' => 'Customer not found'], 404);
            }

            DB::table('customer_sage_statements')->updateOrInsert(
                ['customer_id' => $profession->customer_id],
                [
                    'sage_customer_code' => $validated['customerCode'],
                    'current_balance' => $validated['currentBalance'],
                    'current' => $validated['current'],
                    'days_30' => $validated['days30'],
                    'days_60' => $validated['days60'],
                    'days_90_plus' => $validated['days90Plus'],
                    'as_of_date' => Carbon::parse($validated['asOfUtc']),
                    'updated_at' => now(),
                ]
            );

            return response()->json(['message' => 'Statement received'], 200);
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('SageConnector: receiveStatement failed', ['error' => $e->getMessage(), 'request' => $request->all()]);

            return response()->json(['error' => 'Internal server error'], 500);
        }
    }
}
