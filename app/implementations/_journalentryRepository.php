<?php

namespace App\implementations;

use App\Interfaces\iaccountingperiodInterface;
use App\Interfaces\iaudittrailInterface;
use App\Interfaces\ijournalentryInterface;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class _journalentryRepository implements ijournalentryInterface
{
    protected $model;
    protected $line;
    protected $periodRepo;
    protected $auditRepo;

    public function __construct(
        JournalEntry $model,
        JournalEntryLine $line,
        iaccountingperiodInterface $periodRepo,
        iaudittrailInterface $auditRepo
    ) {
        $this->model      = $model;
        $this->line       = $line;
        $this->periodRepo = $periodRepo;
        $this->auditRepo  = $auditRepo;
    }

    public function getAll($search = null, $status = null, $periodId = null)
    {
        return $this->model
            ->with('currency', 'accountingPeriod', 'createdBy')
            ->when($search, fn($q) => $q->where('reference_number', 'like', "%$search%")->orWhere('description', 'like', "%$search%"))
            ->when($status,   fn($q) => $q->where('status', $status))
            ->when($periodId, fn($q) => $q->where('accounting_period_id', $periodId))
            ->orderByDesc('entry_date')
            ->orderByDesc('id')
            ->paginate(50);
    }

    public function get($id)
    {
        return $this->model
            ->with('lines.account', 'lines.costCenter', 'lines.taxRate', 'currency', 'accountingPeriod', 'createdBy', 'approvedBy', 'postedBy')
            ->find($id);
    }

    public function create($data, $lines)
    {
        DB::beginTransaction();
        try {
            $this->validateLines($lines);

            $data['uuid']             = Str::uuid()->toString();
            $data['reference_number'] = $this->generateReference();
            $data['createdby']        = Auth::id();
            $data['status']           = 'DRAFT';
            $data['total_debit']      = collect($lines)->sum('debit');
            $data['total_credit']     = collect($lines)->sum('credit');

            if (!$this->isBalanced($lines)) {
                DB::rollBack();
                return ['status' => 'error', 'message' => 'Journal entry is not balanced (debits ≠ credits)'];
            }

            $entry = $this->model->create($data);
            $this->saveLines($entry->id, $lines);

            $this->auditRepo->log('JOURNAL', 'CREATE', JournalEntry::class, $entry->id, $entry->reference_number, null, $data);

            DB::commit();
            return ['status' => 'success', 'message' => 'Journal entry created successfully', 'data' => $entry->id];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function update($id, $data, $lines)
    {
        DB::beginTransaction();
        try {
            $entry = $this->model->find($id);
            if (!$entry) return ['status' => 'error', 'message' => 'Journal entry not found'];
            if ($entry->status !== 'DRAFT') return ['status' => 'error', 'message' => 'Only DRAFT entries can be edited'];

            $this->validateLines($lines);
            if (!$this->isBalanced($lines)) {
                DB::rollBack();
                return ['status' => 'error', 'message' => 'Journal entry is not balanced (debits ≠ credits)'];
            }

            $data['total_debit']  = collect($lines)->sum('debit');
            $data['total_credit'] = collect($lines)->sum('credit');

            $old = $entry->toArray();
            $entry->update($data);

            // Replace lines
            $this->line->where('journal_entry_id', $id)->delete();
            $this->saveLines($id, $lines);

            $this->auditRepo->log('JOURNAL', 'UPDATE', JournalEntry::class, $id, $entry->reference_number, $old, $data);

            DB::commit();
            return ['status' => 'success', 'message' => 'Journal entry updated successfully'];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function delete($id)
    {
        DB::beginTransaction();
        try {
            $entry = $this->model->find($id);
            if (!$entry) return ['status' => 'error', 'message' => 'Journal entry not found'];
            if ($entry->status !== 'DRAFT') return ['status' => 'error', 'message' => 'Only DRAFT entries can be deleted'];

            $this->line->where('journal_entry_id', $id)->delete();
            $entry->delete();

            $this->auditRepo->log('JOURNAL', 'DELETE', JournalEntry::class, $id, $entry->reference_number, null, null);

            DB::commit();
            return ['status' => 'success', 'message' => 'Journal entry deleted successfully'];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function post($id)
    {
        DB::beginTransaction();
        try {
            $entry = $this->model->with('lines')->find($id);
            if (!$entry) return ['status' => 'error', 'message' => 'Journal entry not found'];
            if ($entry->status !== 'DRAFT') return ['status' => 'error', 'message' => 'Only DRAFT entries can be posted'];
            if (!$this->isBalanced($entry->lines->toArray())) {
                return ['status' => 'error', 'message' => 'Cannot post unbalanced journal entry'];
            }

            // Resolve period
            if (!$entry->accounting_period_id) {
                $period = $this->periodRepo->getCurrentPeriod();
                if (!$period) {
                    DB::rollBack();
                    return ['status' => 'error', 'message' => 'No open accounting period found for today'];
                }
                $entry->accounting_period_id = $period->id;
            }

            $entry->update([
                'status'    => 'POSTED',
                'posted_by' => Auth::id(),
                'posted_at' => now(),
            ]);

            $this->auditRepo->log('JOURNAL', 'POST', JournalEntry::class, $id, $entry->reference_number, null, ['status' => 'POSTED']);

            DB::commit();
            return ['status' => 'success', 'message' => 'Journal entry posted successfully'];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function reverse($id, $reason)
    {
        DB::beginTransaction();
        try {
            $entry = $this->model->with('lines')->find($id);
            if (!$entry) return ['status' => 'error', 'message' => 'Journal entry not found'];
            if ($entry->status !== 'POSTED') return ['status' => 'error', 'message' => 'Only POSTED entries can be reversed'];

            // Build reversal lines (swap debit/credit)
            $reversalLines = $entry->lines->map(fn($l) => [
                'account_id'     => $l->account_id,
                'cost_center_id' => $l->cost_center_id,
                'description'    => 'Reversal: ' . $l->description,
                'debit'          => $l->credit,
                'credit'         => $l->debit,
                'base_debit'     => $l->base_credit,
                'base_credit'    => $l->base_debit,
                'line_number'    => $l->line_number,
                'tax_rate_id'    => $l->tax_rate_id,
                'tax_amount'     => $l->tax_amount,
            ])->toArray();

            $reversalData = [
                'entry_date'            => now()->toDateString(),
                'description'           => 'Reversal of ' . $entry->reference_number . ' — ' . $reason,
                'entry_type'            => 'REVERSAL',
                'source'                => 'journal_entries',
                'source_id'             => $entry->id,
                'accounting_period_id'  => $entry->accounting_period_id,
                'currency_id'           => $entry->currency_id,
                'exchange_rate'         => $entry->exchange_rate,
            ];

            $result = $this->create($reversalData, $reversalLines);
            if ($result['status'] !== 'success') {
                DB::rollBack();
                return $result;
            }

            $reversalEntry = $this->model->find($result['data']);
            $reversalEntry->update(['status' => 'POSTED', 'posted_by' => Auth::id(), 'posted_at' => now()]);

            $entry->update(['status' => 'REVERSED', 'reversed_by_entry_id' => $reversalEntry->id]);

            $this->auditRepo->log('JOURNAL', 'REVERSE', JournalEntry::class, $id, $entry->reference_number, null, ['reversed_by' => $reversalEntry->reference_number]);

            DB::commit();
            return ['status' => 'success', 'message' => 'Journal entry reversed successfully'];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function generateReference(): string
    {
        $year  = date('Y');
        $count = $this->model->whereYear('created_at', $year)->count() + 1;
        return 'JNL-' . $year . '-' . str_pad($count, 5, '0', STR_PAD_LEFT);
    }

    private function saveLines(int $entryId, array $lines): void
    {
        foreach ($lines as $i => $line) {
            $this->line->create([
                'journal_entry_id' => $entryId,
                'account_id'       => $line['account_id'],
                'cost_center_id'   => $line['cost_center_id'] ?? null,
                'description'      => $line['description'] ?? null,
                'debit'            => $line['debit'] ?? 0,
                'credit'           => $line['credit'] ?? 0,
                'base_debit'       => $line['base_debit'] ?? ($line['debit'] ?? 0),
                'base_credit'      => $line['base_credit'] ?? ($line['credit'] ?? 0),
                'line_number'      => $i + 1,
                'tax_rate_id'      => $line['tax_rate_id'] ?? null,
                'tax_amount'       => $line['tax_amount'] ?? 0,
            ]);
        }
    }

    private function validateLines(array $lines): void
    {
        if (count($lines) < 2) {
            throw new \Exception('A journal entry requires at least two lines');
        }
        foreach ($lines as $line) {
            if (empty($line['account_id'])) throw new \Exception('All lines must have an account');
            $debit  = (float) ($line['debit']  ?? 0);
            $credit = (float) ($line['credit'] ?? 0);
            if ($debit < 0 || $credit < 0) throw new \Exception('Debit/credit amounts cannot be negative');
            if ($debit > 0 && $credit > 0)  throw new \Exception('A line cannot have both a debit and a credit');
            if ($debit === 0.0 && $credit === 0.0) throw new \Exception('A line must have either a debit or a credit');
        }
    }

    private function isBalanced(array $lines): bool
    {
        $debit  = round(collect($lines)->sum('debit'), 2);
        $credit = round(collect($lines)->sum('credit'), 2);
        return $debit === $credit;
    }
}
