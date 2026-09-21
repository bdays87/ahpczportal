<?php

namespace App\implementations;

use App\Interfaces\ifinancialreportInterface;
use App\Models\ChartOfAccount;
use App\Models\JournalEntryLine;
use Illuminate\Support\Facades\DB;

class _financialreportRepository implements ifinancialreportInterface
{
    protected $account;
    protected $line;

    public function __construct(ChartOfAccount $account, JournalEntryLine $line)
    {
        $this->account = $account;
        $this->line    = $line;
    }

    /**
     * Trial Balance: list every account with total debits, total credits, and net balance.
     */
    public function getTrialBalance($periodId = null, $from = null, $to = null)
    {
        $lines = $this->line
            ->select('account_id', DB::raw('SUM(debit) as total_debit'), DB::raw('SUM(credit) as total_credit'))
            ->whereHas('journalEntry', function ($q) use ($periodId, $from, $to) {
                $q->where('status', 'POSTED');
                if ($periodId) $q->where('accounting_period_id', $periodId);
                if ($from)     $q->whereDate('entry_date', '>=', $from);
                if ($to)       $q->whereDate('entry_date', '<=', $to);
            })
            ->groupBy('account_id')
            ->get()
            ->keyBy('account_id');

        $accounts = $this->account->with('parent')->where('status', 'active')->orderBy('code')->get();

        $rows = [];
        $totalDebit  = 0;
        $totalCredit = 0;

        foreach ($accounts as $account) {
            $data = $lines->get($account->id);
            $debit  = $data ? (float) $data->total_debit  : 0;
            $credit = $data ? (float) $data->total_credit : 0;
            if ($debit == 0 && $credit == 0) continue;

            $totalDebit  += $debit;
            $totalCredit += $credit;

            $rows[] = [
                'code'          => $account->code,
                'name'          => $account->name,
                'account_type'  => $account->account_type,
                'total_debit'   => $debit,
                'total_credit'  => $credit,
                'balance'       => in_array($account->account_type, ['ASSET', 'EXPENSE'])
                    ? $debit - $credit
                    : $credit - $debit,
            ];
        }

        return [
            'rows'          => $rows,
            'total_debit'   => $totalDebit,
            'total_credit'  => $totalCredit,
            'is_balanced'   => round($totalDebit, 2) === round($totalCredit, 2),
        ];
    }

    /**
     * Profit & Loss: INCOME minus EXPENSE accounts grouped by sub-type.
     */
    public function getProfitAndLoss($periodId = null, $from = null, $to = null)
    {
        $lines = $this->getPostedLinesGrouped($periodId, $from, $to);
        $accounts = $this->account->where('status', 'active')->whereIn('account_type', ['INCOME', 'EXPENSE'])->orderBy('code')->get();

        $income  = [];
        $expense = [];
        $totalIncome  = 0;
        $totalExpense = 0;

        foreach ($accounts as $account) {
            $data   = $lines->get($account->id);
            $debit  = $data ? (float) $data->total_debit  : 0;
            $credit = $data ? (float) $data->total_credit : 0;
            if ($debit == 0 && $credit == 0) continue;

            $balance = $account->account_type === 'INCOME' ? $credit - $debit : $debit - $credit;

            $row = [
                'code'         => $account->code,
                'name'         => $account->name,
                'subtype'      => $account->account_subtype,
                'balance'      => $balance,
            ];

            if ($account->account_type === 'INCOME') {
                $income[]      = $row;
                $totalIncome  += $balance;
            } else {
                $expense[]     = $row;
                $totalExpense += $balance;
            }
        }

        return [
            'income'        => $income,
            'expense'       => $expense,
            'total_income'  => $totalIncome,
            'total_expense' => $totalExpense,
            'net_profit'    => $totalIncome - $totalExpense,
        ];
    }

    /**
     * Balance Sheet: ASSET, LIABILITY, EQUITY accounts as at a date.
     */
    public function getBalanceSheet($periodId = null, $asAt = null)
    {
        $lines = $this->getPostedLinesGrouped($periodId, null, $asAt);
        $accounts = $this->account->where('status', 'active')
            ->whereIn('account_type', ['ASSET', 'LIABILITY', 'EQUITY'])
            ->orderBy('code')
            ->get();

        $assets      = [];
        $liabilities = [];
        $equity      = [];
        $totalAssets = $totalLiabilities = $totalEquity = 0;

        foreach ($accounts as $account) {
            $data    = $lines->get($account->id);
            $debit   = $data ? (float) $data->total_debit  : 0;
            $credit  = $data ? (float) $data->total_credit : 0;
            if ($debit == 0 && $credit == 0) continue;

            $balance = $account->account_type === 'ASSET' ? $debit - $credit : $credit - $debit;

            $row = ['code' => $account->code, 'name' => $account->name, 'subtype' => $account->account_subtype, 'balance' => $balance];

            if ($account->account_type === 'ASSET') {
                $assets[]       = $row;
                $totalAssets   += $balance;
            } elseif ($account->account_type === 'LIABILITY') {
                $liabilities[]    = $row;
                $totalLiabilities += $balance;
            } else {
                $equity[]       = $row;
                $totalEquity   += $balance;
            }
        }

        return [
            'assets'            => $assets,
            'liabilities'       => $liabilities,
            'equity'            => $equity,
            'total_assets'      => $totalAssets,
            'total_liabilities' => $totalLiabilities,
            'total_equity'      => $totalEquity,
            'is_balanced'       => round($totalAssets, 2) === round($totalLiabilities + $totalEquity, 2),
        ];
    }

    /**
     * Cash Flow: summarise movements in cash/bank accounts.
     */
    public function getCashFlow($periodId = null, $from = null, $to = null)
    {
        // Cash accounts are ASSET accounts with subtype 'CASH' or 'BANK'
        $cashAccounts = $this->account
            ->where('status', 'active')
            ->where('account_type', 'ASSET')
            ->whereIn('account_subtype', ['CASH', 'BANK', 'Cash', 'Bank'])
            ->pluck('id');

        $inflows = $this->line
            ->whereIn('account_id', $cashAccounts)
            ->whereHas('journalEntry', function ($q) use ($periodId, $from, $to) {
                $q->where('status', 'POSTED');
                if ($periodId) $q->where('accounting_period_id', $periodId);
                if ($from)     $q->whereDate('entry_date', '>=', $from);
                if ($to)       $q->whereDate('entry_date', '<=', $to);
            })
            ->sum('debit');

        $outflows = $this->line
            ->whereIn('account_id', $cashAccounts)
            ->whereHas('journalEntry', function ($q) use ($periodId, $from, $to) {
                $q->where('status', 'POSTED');
                if ($periodId) $q->where('accounting_period_id', $periodId);
                if ($from)     $q->whereDate('entry_date', '>=', $from);
                if ($to)       $q->whereDate('entry_date', '<=', $to);
            })
            ->sum('credit');

        return [
            'total_inflows'  => (float) $inflows,
            'total_outflows' => (float) $outflows,
            'net_cash_flow'  => (float) $inflows - (float) $outflows,
        ];
    }

    /**
     * General Ledger for a specific account with running balance.
     */
    public function getGeneralLedger($accountId = null, $from = null, $to = null)
    {
        $query = $this->line
            ->with('journalEntry.currency', 'costCenter')
            ->whereHas('journalEntry', function ($q) use ($from, $to) {
                $q->where('status', 'POSTED');
                if ($from) $q->whereDate('entry_date', '>=', $from);
                if ($to)   $q->whereDate('entry_date', '<=', $to);
            })
            ->orderBy(
                \App\Models\JournalEntry::select('entry_date')
                    ->whereColumn('journal_entries.id', 'journal_entry_lines.journal_entry_id')
                    ->limit(1)
            )
            ->orderBy('id');

        if ($accountId) {
            $query->where('account_id', $accountId);
        }

        $lines = $query->get();

        $account = $this->account->find($accountId);
        $runningBalance = 0;
        $isDebitNormal  = $account && in_array($account->account_type, ['ASSET', 'EXPENSE']);

        $rows = $lines->map(function ($line) use (&$runningBalance, $isDebitNormal) {
            $runningBalance += $isDebitNormal
                ? ($line->debit - $line->credit)
                : ($line->credit - $line->debit);

            return [
                'date'            => $line->journalEntry->entry_date,
                'reference'       => $line->journalEntry->reference_number,
                'description'     => $line->description ?? $line->journalEntry->description,
                'debit'           => $line->debit,
                'credit'          => $line->credit,
                'running_balance' => $runningBalance,
            ];
        });

        return [
            'account' => $account,
            'rows'    => $rows,
        ];
    }

    private function getPostedLinesGrouped($periodId, $from, $to)
    {
        return $this->line
            ->select('account_id', DB::raw('SUM(debit) as total_debit'), DB::raw('SUM(credit) as total_credit'))
            ->whereHas('journalEntry', function ($q) use ($periodId, $from, $to) {
                $q->where('status', 'POSTED');
                if ($periodId) $q->where('accounting_period_id', $periodId);
                if ($from)     $q->whereDate('entry_date', '>=', $from);
                if ($to)       $q->whereDate('entry_date', '<=', $to);
            })
            ->groupBy('account_id')
            ->get()
            ->keyBy('account_id');
    }
}
