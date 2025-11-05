<?php

namespace App\implementations;

use App\Interfaces\irevenueInterface;
use App\Models\Invoice;
use App\Models\Manualpayment;

class _revenueRepository implements irevenueInterface
{
    /**
     * Create a new class instance.
     */
    protected $invoice;

    protected $manualpayment;

    public function __construct(Invoice $invoice, Manualpayment $manualpayment)
    {
        $this->invoice = $invoice;
        $this->manualpayment = $manualpayment;
    }

    public function reportData(array $filters = [])
    {
        $invoices = $this->getFilteredInvoices($filters);
        $manualPayments = $this->getFilteredManualPayments($filters);

        // Combine and format the data
        $combined = collect();

        foreach ($invoices as $invoice) {
            $combined->push([
                'id' => $invoice->id,
                'type' => 'Invoice',
                'transaction_date' => $invoice->created_at,
                'customer' => $invoice->customer,
                'description' => $invoice->description,
                'amount' => $invoice->amount,
                'currency' => $invoice->currency,
                'status' => $invoice->status,
                'source' => $invoice->source,
                'year' => $invoice->year,
                'reference' => $invoice->invoice_number,
            ]);
        }

        foreach ($manualPayments as $payment) {
            $combined->push([
                'id' => $payment->id,
                'type' => 'Manual Payment',
                'transaction_date' => $payment->created_at,
                'customer' => $payment->customer,
                'description' => 'Manual Payment - '.$payment->mode,
                'amount' => $payment->amount,
                'currency' => $payment->currency,
                'status' => 'PAID',
                'source' => $payment->mode,
                'year' => $payment->year,
                'reference' => 'MP-'.$payment->id,
            ]);
        }

        return $combined->sortByDesc('transaction_date')->values();
    }

    protected function getFilteredInvoices(array $filters)
    {
        $query = $this->invoice->with(['customer.province', 'customer.city', 'currency']);

        // Filter by date range
        if (! empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }
        if (! empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        // Filter by year
        if (! empty($filters['year'])) {
            $query->where('year', $filters['year']);
        }

        // Filter by status
        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Filter by source
        if (! empty($filters['source'])) {
            $query->where('source', $filters['source']);
        }

        // Filter by province
        if (! empty($filters['province_id'])) {
            $query->whereHas('customer', function ($q) use ($filters) {
                $q->where('province_id', $filters['province_id']);
            });
        }

        // Filter by city
        if (! empty($filters['city_id'])) {
            $query->whereHas('customer', function ($q) use ($filters) {
                $q->where('city_id', $filters['city_id']);
            });
        }

        // Search by customer name or invoice number
        if (! empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('invoice_number', 'like', '%'.$filters['search'].'%')
                    ->orWhereHas('customer', function ($subQ) use ($filters) {
                        $subQ->where('name', 'like', '%'.$filters['search'].'%')
                            ->orWhere('surname', 'like', '%'.$filters['search'].'%')
                            ->orWhere('email', 'like', '%'.$filters['search'].'%');
                    });
            });
        }

        return $query->get();
    }

    protected function getFilteredManualPayments(array $filters)
    {
        // Only include manual payments if not filtering by specific invoice statuses
        if (! empty($filters['status']) && ! in_array($filters['status'], ['PAID', 'SETTLED'])) {
            return collect();
        }

        $query = $this->manualpayment->with(['customer.province', 'customer.city', 'currency']);

        // Filter by date range
        if (! empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }
        if (! empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        // Filter by year
        if (! empty($filters['year'])) {
            $query->where('year', $filters['year']);
        }

        // Filter by payment mode (source)
        if (! empty($filters['source'])) {
            $query->where('mode', $filters['source']);
        }

        // Filter by province
        if (! empty($filters['province_id'])) {
            $query->whereHas('customer', function ($q) use ($filters) {
                $q->where('province_id', $filters['province_id']);
            });
        }

        // Filter by city
        if (! empty($filters['city_id'])) {
            $query->whereHas('customer', function ($q) use ($filters) {
                $q->where('city_id', $filters['city_id']);
            });
        }

        // Search by customer name
        if (! empty($filters['search'])) {
            $query->whereHas('customer', function ($q) use ($filters) {
                $q->where('name', 'like', '%'.$filters['search'].'%')
                    ->orWhere('surname', 'like', '%'.$filters['search'].'%')
                    ->orWhere('email', 'like', '%'.$filters['search'].'%');
            });
        }

        return $query->get();
    }

    public function getSummaryStats(array $filters = [])
    {
        $data = $this->reportData($filters);

        return [
            'total_revenue' => $data->sum('amount'),
            'total_transactions' => $data->count(),
            'invoice_count' => $data->where('type', 'Invoice')->count(),
            'manual_payment_count' => $data->where('type', 'Manual Payment')->count(),
            'paid_amount' => $data->where('status', 'PAID')->sum('amount'),
            'pending_amount' => $data->whereIn('status', ['PENDING', 'AWAITING'])->sum('amount'),
        ];
    }
}




