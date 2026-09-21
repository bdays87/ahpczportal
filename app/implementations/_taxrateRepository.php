<?php

namespace App\implementations;

use App\Interfaces\itaxrateInterface;
use App\Models\TaxRate;
use App\Models\TaxTransaction;
use Illuminate\Support\Facades\Auth;

class _taxrateRepository implements itaxrateInterface
{
    protected $model;
    protected $transaction;

    public function __construct(TaxRate $model, TaxTransaction $transaction)
    {
        $this->model       = $model;
        $this->transaction = $transaction;
    }

    public function getAll($status = null)
    {
        return $this->model
            ->with('salesAccount', 'purchaseAccount')
            ->when($status, fn($q) => $q->where('status', $status))
            ->orderBy('name')
            ->get();
    }

    public function get($id)
    {
        return $this->model->with('salesAccount', 'purchaseAccount')->find($id);
    }

    public function create($data)
    {
        try {
            $exists = $this->model->where('code', $data['code'])->exists();
            if ($exists) return ['status' => 'error', 'message' => 'Tax rate code already exists'];
            $data['createdby'] = Auth::id();
            $this->model->create($data);
            return ['status' => 'success', 'message' => 'Tax rate created successfully'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function update($id, $data)
    {
        try {
            $rate = $this->model->find($id);
            if (!$rate) return ['status' => 'error', 'message' => 'Tax rate not found'];
            $codeExists = $this->model->where('code', $data['code'])->where('id', '!=', $id)->exists();
            if ($codeExists) return ['status' => 'error', 'message' => 'Code already used by another tax rate'];
            $rate->update($data);
            return ['status' => 'success', 'message' => 'Tax rate updated successfully'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function delete($id)
    {
        try {
            $rate = $this->model->find($id);
            if (!$rate) return ['status' => 'error', 'message' => 'Tax rate not found'];
            if ($rate->taxTransactions()->exists()) {
                return ['status' => 'error', 'message' => 'Cannot delete tax rate with existing transactions'];
            }
            $rate->delete();
            return ['status' => 'success', 'message' => 'Tax rate deleted successfully'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function getTaxReport($periodId = null, $from = null, $to = null)
    {
        $rates = $this->model->where('status', 'active')->get();
        $report = [];
        foreach ($rates as $rate) {
            $query = $this->transaction->where('tax_rate_id', $rate->id)->where('status', 'POSTED');
            if ($periodId) $query->where('accounting_period_id', $periodId);
            if ($from)     $query->whereDate('transaction_date', '>=', $from);
            if ($to)       $query->whereDate('transaction_date', '<=', $to);

            $inputTax  = (float) $query->clone()->where('direction', 'INPUT')->sum('tax_amount');
            $outputTax = (float) $query->clone()->where('direction', 'OUTPUT')->sum('tax_amount');

            $report[] = [
                'tax_rate'       => $rate->name,
                'code'           => $rate->code,
                'rate'           => $rate->rate,
                'input_tax'      => $inputTax,
                'output_tax'     => $outputTax,
                'net_tax'        => $outputTax - $inputTax,
            ];
        }
        return $report;
    }
}
