<?php

namespace App\implementations;

use App\Interfaces\isupplierInterface;
use App\Models\Supplier;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class _supplierRepository implements isupplierInterface
{
    protected $model;

    public function __construct(Supplier $model)
    {
        $this->model = $model;
    }

    public function getAll($search = null, $status = null)
    {
        return $this->model
            ->with('currency')
            ->when($search, fn($q) => $q->where('name', 'like', "%$search%")->orWhere('code', 'like', "%$search%")->orWhere('email', 'like', "%$search%"))
            ->when($status, fn($q) => $q->where('status', $status))
            ->orderBy('name')
            ->get();
    }

    public function get($id)
    {
        return $this->model->with('currency', 'apAccount', 'taxRate', 'invoices.currency', 'payments.currency')->find($id);
    }

    public function create($data)
    {
        try {
            if (empty($data['code'])) {
                $data['code'] = $this->generateCode();
            }
            $exists = $this->model->where('code', $data['code'])->exists();
            if ($exists) return ['status' => 'error', 'message' => 'Supplier code already exists'];
            $data['uuid']      = Str::uuid()->toString();
            $data['createdby'] = Auth::id();
            $this->model->create($data);
            return ['status' => 'success', 'message' => 'Supplier created successfully'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function update($id, $data)
    {
        try {
            $supplier = $this->model->find($id);
            if (!$supplier) return ['status' => 'error', 'message' => 'Supplier not found'];
            $codeExists = $this->model->where('code', $data['code'])->where('id', '!=', $id)->exists();
            if ($codeExists) return ['status' => 'error', 'message' => 'Supplier code already used'];
            $supplier->update($data);
            return ['status' => 'success', 'message' => 'Supplier updated successfully'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function delete($id)
    {
        try {
            $supplier = $this->model->find($id);
            if (!$supplier) return ['status' => 'error', 'message' => 'Supplier not found'];
            if ($supplier->invoices()->exists()) return ['status' => 'error', 'message' => 'Cannot delete supplier with invoices'];
            if ($supplier->payments()->exists()) return ['status' => 'error', 'message' => 'Cannot delete supplier with payments'];
            $supplier->delete();
            return ['status' => 'success', 'message' => 'Supplier deleted successfully'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function getBalance($id)
    {
        $supplier = $this->model->find($id);
        if (!$supplier) return 0;
        return $supplier->getBalance();
    }

    public function getStatement($id, $from = null, $to = null)
    {
        $supplier = $this->model->with([
            'invoices' => fn($q) => $q->with('currency')
                ->when($from, fn($q2) => $q2->whereDate('invoice_date', '>=', $from))
                ->when($to,   fn($q2) => $q2->whereDate('invoice_date', '<=', $to)),
            'payments' => fn($q) => $q->with('currency')
                ->when($from, fn($q2) => $q2->whereDate('payment_date', '>=', $from))
                ->when($to,   fn($q2) => $q2->whereDate('payment_date', '<=', $to)),
        ])->find($id);

        return $supplier;
    }

    private function generateCode(): string
    {
        $last = $this->model->orderByDesc('id')->first();
        $nextNum = $last ? ((int) ltrim(substr($last->code, 3), '0') + 1) : 1;
        return 'SUP' . str_pad($nextNum, 4, '0', STR_PAD_LEFT);
    }
}
