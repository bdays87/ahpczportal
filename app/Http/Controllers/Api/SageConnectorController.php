<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Customerprofession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SageConnectorController extends Controller
{
    /**
     * GET /api/connector/customers/pending
     * 
     * Returns customers/practitioners awaiting sync to Sage
     * Query param: since (datetime) - optional, defaults to 7 days ago
     * 
     * Only returns:
     * - Customers with APPROVED registrations
     * - Customers with APPROVED applications
     * - Customers not yet synced or failed sync
     */
    public function getPendingCustomers(Request $request)
    {
        try {
            // Default to last 7 days if not specified
            $since = $request->input('since', Carbon::now()->subDays(7)->toIso8601String());
            $sinceDate = Carbon::parse($since);

            // Get customers with approved registrations AND approved applications
            $customers = Customer::query()
                ->whereHas('customerprofessions', function ($query) use ($sinceDate) {
                    $query->where('status', 'APPROVED')
                        ->where('updated_at', '>=', $sinceDate)
                        // Must have approved application
                        ->whereHas('applications', function ($appQuery) {
                            $appQuery->where('status', 'APPROVED');
                        })
                        // Must have approved registration
                        ->whereHas('registration', function ($regQuery) {
                            $regQuery->where('status', 'APPROVED');
                        })
                        ->where(function ($q) {
                            // Not synced OR sync failed
                            $q->whereNull('sage_sync_status')
                              ->orWhere('sage_sync_status', 'PENDING')
                              ->orWhere('sage_sync_status', 'FAILED');
                        });
                })
                ->with(['customerprofessions' => function ($query) {
                    $query->where('status', 'APPROVED')
                          ->with(['registration', 'applications', 'profession']);
                }])
                ->get()
                ->map(function ($customer) {
                    $profession = $customer->customerprofessions->first();
                    
                    // Generate unique customer code for Sage
                    // Format: PREFIX-REGNUM or PREFIX-UUID
                    $customerCode = $this->generateSageCustomerCode($customer, $profession);
                    
                    return [
                        'externalId' => $customerCode,
                        'name' => trim($customer->name . ' ' . $customer->surname),
                        'contact' => trim($customer->name . ' ' . $customer->surname),
                        'email' => $customer->email,
                        'phone' => $customer->phone,
                        'vatNumber' => $customer->vat_number ?? null,
                        'physicalAddress' => $customer->address,
                        'createdUtc' => $profession?->updated_at?->toIso8601String() ?? $customer->created_at->toIso8601String(),
                    ];
                });

            return response()->json($customers);
        } catch (\Exception $e) {
            Log::error('SageConnector: getPendingCustomers failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    /**
     * Generate unique Sage customer code
     * Format: PROF-REGNO or PROF-UUID
     */
    private function generateSageCustomerCode($customer, $profession)
    {
        if (!$profession) {
            return 'CUST-' . $customer->id;
        }

        // Use registration certificate number if available
        if ($profession->registration && $profession->registration->certificatenumber) {
            return $profession->registration->certificatenumber;
        }

        // Use profession prefix + registration ID
        $prefix = $profession->profession->prefix ?? 'REG';
        
        if ($profession->registration && $profession->registration->id) {
            return $prefix . '-' . str_pad($profession->registration->id, 5, '0', STR_PAD_LEFT);
        }

        // Fallback to UUID
        return $customer->uuid ?? 'CUST-' . $customer->id;
    }

    /**
     * GET /api/connector/orders/pending
     * 
     * Returns invoices awaiting creation in Sage
     * Query param: since (datetime) - optional, defaults to 7 days ago
     * 
     * Only returns:
     * - PAID invoices
     * - Customers already synced to Sage
     * - Not yet synced or failed sync
     */
    public function getPendingOrders(Request $request)
    {
        try {
            $since = $request->input('since', Carbon::now()->subDays(7)->toIso8601String());
            $sinceDate = Carbon::parse($since);

            // Get paid invoices for customers already synced to Sage
            $invoices = Invoice::query()
                ->with(['customer.customerprofessions.registration', 'customer.customerprofessions.profession', 'invoicelines'])
                ->where('status', 'PAID')
                ->where('created_at', '>=', $sinceDate)
                // Customer must be synced to Sage first
                ->whereHas('customer.customerprofessions', function ($query) {
                    $query->where('sage_sync_status', 'SYNCED')
                          ->whereNotNull('sage_customer_code');
                })
                // Invoice not yet synced
                ->where(function ($query) {
                    $query->whereNull('sage_sync_status')
                          ->orWhere('sage_sync_status', 'PENDING')
                          ->orWhere('sage_sync_status', 'FAILED');
                })
                ->get()
                ->map(function ($invoice) {
                    // Get customer's Sage code
                    $profession = $invoice->customer->customerprofessions()
                        ->where('sage_sync_status', 'SYNCED')
                        ->whereNotNull('sage_customer_code')
                        ->first();
                    
                    if (!$profession || !$profession->sage_customer_code) {
                        // Skip this invoice - customer not synced
                        return null;
                    }

                    // Build invoice lines
                    $lines = [];
                    
                    if ($invoice->invoicelines && $invoice->invoicelines->count() > 0) {
                        foreach ($invoice->invoicelines as $line) {
                            $lines[] = [
                                'stockCode' => $line->stock_code ?? 'SERVICE-' . $line->id,
                                'description' => $line->description ?? $invoice->description,
                                'quantity' => $line->quantity ?? 1,
                                'unitPrice' => (float) ($line->unit_price ?? $line->amount ?? 0),
                            ];
                        }
                    } else {
                        // Create a single line from invoice total
                        $stockCode = 'SERVICE-' . strtoupper(str_replace(' ', '-', $invoice->description ?? 'GENERAL'));
                        $lines[] = [
                            'stockCode' => $stockCode,
                            'description' => $invoice->description ?? 'Service Fee',
                            'quantity' => 1,
                            'unitPrice' => (float) $invoice->amount,
                        ];
                    }

                    return [
                        'externalId' => $invoice->invoice_number,
                        'customerExternalId' => $profession->sage_customer_code, // Use Sage code, not portal ID
                        'sageCustomerCode' => $profession->sage_customer_code,
                        'orderDate' => $invoice->created_at->toIso8601String(),
                        'reference' => $invoice->description . ' - ' . $invoice->invoice_number,
                        'lines' => $lines,
                    ];
                })
                ->filter() // Remove nulls
                ->values();

            return response()->json($invoices);
        } catch (\Exception $e) {
            Log::error('SageConnector: getPendingOrders failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    /**
     * POST /api/connector/ack
     * 
     * Receives acknowledgment of sync success/failure from Sage
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
                // Find customer by Sage customer code (externalId)
                // This could be certificate number, REG-XXXXX, or UUID
                $profession = Customerprofession::where('sage_customer_code', $validated['externalId'])->first();
                
                if (!$profession) {
                    // Try finding by registration certificate number
                    $profession = Customerprofession::whereHas('registration', function ($query) use ($validated) {
                        $query->where('certificatenumber', $validated['externalId']);
                    })->first();
                }
                
                if (!$profession) {
                    // Try finding by customer UUID
                    $customer = Customer::where('uuid', $validated['externalId'])
                        ->orWhere(DB::raw("CONCAT('CUST-', id)"), $validated['externalId'])
                        ->first();
                    
                    if ($customer) {
                        $profession = $customer->customerprofessions()
                            ->where('status', 'APPROVED')
                            ->first();
                    }
                }

                if ($profession) {
                    $profession->update([
                        'sage_sync_status' => $validated['success'] ? 'SYNCED' : 'FAILED',
                        'sage_customer_code' => $validated['sageReference'] ?? $validated['externalId'], // Use sageReference if provided
                        'sage_sync_error' => $validated['errorMessage'] ?? null,
                        'sage_synced_at' => $validated['processedUtc'],
                    ]);

                    Log::info('SageConnector: Customer sync ack received', [
                        'externalId' => $validated['externalId'],
                        'success' => $validated['success'],
                        'sageCustomerCode' => $validated['sageReference'] ?? $validated['externalId'],
                    ]);
                } else {
                    Log::warning('SageConnector: Customer not found for ack', [
                        'externalId' => $validated['externalId'],
                    ]);
                }
            } elseif ($validated['kind'] === 'order') {
                // Find invoice by invoice number
                $invoice = Invoice::where('invoice_number', $validated['externalId'])->first();

                if ($invoice) {
                    $invoice->update([
                        'sage_sync_status' => $validated['success'] ? 'SYNCED' : 'FAILED',
                        'sage_invoice_number' => $validated['sageReference'] ?? null,
                        'sage_sync_error' => $validated['errorMessage'] ?? null,
                        'sage_synced_at' => $validated['processedUtc'],
                    ]);

                    Log::info('SageConnector: Invoice sync ack received', [
                        'externalId' => $validated['externalId'],
                        'success' => $validated['success'],
                        'sageReference' => $validated['sageReference'],
                    ]);
                } else {
                    Log::warning('SageConnector: Invoice not found for ack', [
                        'externalId' => $validated['externalId'],
                    ]);
                }
            }

            return response()->json(['message' => 'Acknowledgment received'], 200);
        } catch (\Exception $e) {
            Log::error('SageConnector: receiveAck failed', [
                'error' => $e->getMessage(),
                'request' => $request->all(),
            ]);
            
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    /**
     * POST /api/connector/invoices
     * 
     * Receives invoice data pushed from Sage
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
            ]);

            // Find customer by Sage customer code
            $profession = Customerprofession::where('sage_customer_code', $validated['customerCode'])->first();
            
            if (!$profession) {
                // Try finding by UUID
                $customer = Customer::where('uuid', $validated['customerCode'])
                    ->orWhere(DB::raw("CONCAT('CUST-', id)"), $validated['customerCode'])
                    ->first();
                
                if ($customer) {
                    $profession = $customer->customerprofessions()->first();
                }
            }

            if (!$profession) {
                Log::warning('SageConnector: Customer not found for invoice', [
                    'customerCode' => $validated['customerCode'],
                    'invoiceNumber' => $validated['invoiceNumber'],
                ]);
                
                return response()->json(['error' => 'Customer not found'], 404);
            }

            // Update or create invoice record with Sage data
            $invoice = Invoice::where('sage_invoice_number', $validated['invoiceNumber'])->first();
            
            if ($invoice) {
                // Update existing
                $invoice->update([
                    'sage_total_excl_tax' => $validated['totalExclTax'],
                    'sage_total_tax' => $validated['totalTax'],
                    'sage_total_incl_tax' => $validated['totalInclTax'],
                    'sage_outstanding' => $validated['amountOutstanding'],
                    'sage_status' => $validated['status'],
                    'sage_invoice_date' => $validated['invoiceDate'],
                    'sage_last_updated_at' => now(),
                ]);

                Log::info('SageConnector: Invoice updated from Sage', [
                    'invoiceNumber' => $validated['invoiceNumber'],
                    'outstanding' => $validated['amountOutstanding'],
                ]);
            } else {
                Log::info('SageConnector: New invoice received from Sage', [
                    'invoiceNumber' => $validated['invoiceNumber'],
                    'customerCode' => $validated['customerCode'],
                ]);
            }

            return response()->json(['message' => 'Invoice received'], 200);
        } catch (\Exception $e) {
            Log::error('SageConnector: receiveInvoice failed', [
                'error' => $e->getMessage(),
                'request' => $request->all(),
            ]);
            
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    /**
     * POST /api/connector/statements
     * 
     * Receives customer statement/balance data from Sage
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

            // Find customer by Sage customer code
            $profession = Customerprofession::where('sage_customer_code', $validated['customerCode'])->first();
            
            if (!$profession) {
                // Try finding by UUID
                $customer = Customer::where('uuid', $validated['customerCode'])
                    ->orWhere(DB::raw("CONCAT('CUST-', id)"), $validated['customerCode'])
                    ->first();
                
                if ($customer) {
                    $profession = $customer->customerprofessions()->first();
                }
            }

            if (!$profession) {
                Log::warning('SageConnector: Customer not found for statement', [
                    'customerCode' => $validated['customerCode'],
                ]);
                
                return response()->json(['error' => 'Customer not found'], 404);
            }

            // Update or create statement record
            DB::table('customer_sage_statements')->updateOrInsert(
                ['customer_id' => $profession->customer_id],
                [
                    'sage_customer_code' => $validated['customerCode'],
                    'current_balance' => $validated['currentBalance'],
                    'current' => $validated['current'],
                    'days_30' => $validated['days30'],
                    'days_60' => $validated['days60'],
                    'days_90_plus' => $validated['days90Plus'],
                    'as_of_date' => $validated['asOfUtc'],
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );

            Log::info('SageConnector: Statement updated', [
                'customerCode' => $validated['customerCode'],
                'balance' => $validated['currentBalance'],
            ]);

            return response()->json(['message' => 'Statement received'], 200);
        } catch (\Exception $e) {
            Log::error('SageConnector: receiveStatement failed', [
                'error' => $e->getMessage(),
                'request' => $request->all(),
            ]);
            
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }
}
