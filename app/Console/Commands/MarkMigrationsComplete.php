<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MarkMigrationsComplete extends Command
{
    protected $signature = 'migrate:mark-complete {--check : Check which tables exist}';
    
    protected $description = 'Mark existing migrations as complete (for tables that already exist)';

    public function handle()
    {
        if ($this->option('check')) {
            return $this->checkExistingTables();
        }

        $migrations = [
            '2026_07_09_055434_add_provider_test_numbers_filters_to_smsbroadcasts_table' => 'smsbroadcasts',
            '2026_09_01_000001_create_accounting_periods_table' => 'accounting_periods',
            '2026_09_01_000002_create_cost_centers_table' => 'cost_centers',
            '2026_09_01_000003_create_chart_of_accounts_table' => 'chart_of_accounts',
            '2026_09_01_000004_create_tax_rates_table' => 'tax_rates',
            '2026_09_01_000005_create_suppliers_table' => 'suppliers',
            '2026_09_01_000006_create_journal_entries_table' => 'journal_entries',
            '2026_09_01_000007_create_journal_entry_lines_table' => 'journal_entry_lines',
            '2026_09_01_000008_create_accounts_payable_invoices_table' => 'accounts_payable_invoices',
            '2026_09_01_000009_create_accounts_payable_payments_table' => 'accounts_payable_payments',
            '2026_09_01_000010_create_ap_invoice_payments_table' => 'ap_invoice_payments',
            '2026_09_01_000011_create_accounts_receivable_invoices_table' => 'accounts_receivable_invoices',
            '2026_09_01_000012_create_accounts_receivable_receipts_table' => 'accounts_receivable_receipts',
            '2026_09_01_000013_create_ar_invoice_receipts_table' => 'ar_invoice_receipts',
            '2026_09_01_000014_create_budgets_table' => 'budgets',
            '2026_09_01_000015_create_budget_lines_table' => 'budget_lines',
            '2026_09_01_000016_create_tax_transactions_table' => 'tax_transactions',
            '2026_09_01_000017_create_accounting_audit_trail_table' => 'accounting_audit_trails',
        ];

        $this->info('🔍 Checking which tables already exist...');
        $this->newLine();

        $toMark = [];
        $notFound = [];
        $alreadyMarked = [];

        // Get current batch number
        $currentBatch = DB::table('migrations')->max('batch') ?? 0;
        $nextBatch = $currentBatch + 1;

        foreach ($migrations as $migration => $tableName) {
            // Check if migration already recorded
            $exists = DB::table('migrations')->where('migration', $migration)->exists();
            
            if ($exists) {
                $alreadyMarked[] = $migration;
                $this->line("⏭️  Already marked: <comment>$migration</comment>");
                continue;
            }

            // Check if table exists
            $tableExists = Schema::hasTable($tableName);
            
            if ($tableExists) {
                $toMark[] = $migration;
                $this->line("✅ Table exists, will mark: <info>$migration</info>");
            } else {
                $notFound[] = $migration;
                $this->line("⚠️  Table not found: <comment>$migration</comment>");
            }
        }

        $this->newLine();
        
        if (empty($toMark)) {
            $this->info('✅ No migrations to mark. All tables either don\'t exist or are already marked.');
            return 0;
        }

        $this->info('📋 Summary:');
        $this->info("  - To mark as complete: " . count($toMark));
        $this->info("  - Already marked: " . count($alreadyMarked));
        $this->info("  - Tables not found: " . count($notFound));
        $this->newLine();

        if (!$this->confirm('Mark ' . count($toMark) . ' migrations as complete?', true)) {
            $this->info('❌ Cancelled.');
            return 0;
        }

        $this->info('🚀 Marking migrations as complete...');
        
        $marked = 0;
        foreach ($toMark as $migration) {
            DB::table('migrations')->insert([
                'migration' => $migration,
                'batch' => $nextBatch,
            ]);
            $marked++;
            $this->line("  ✓ $migration");
        }

        $this->newLine();
        $this->info("✅ Successfully marked $marked migrations as complete!");
        $this->newLine();
        $this->info('🎉 You can now run: php artisan migrate');
        
        return 0;
    }

    private function checkExistingTables()
    {
        $tables = [
            'smsbroadcasts' => 'provider column',
            'accounting_periods' => 'table',
            'cost_centers' => 'table',
            'chart_of_accounts' => 'table',
            'tax_rates' => 'table',
            'suppliers' => 'table',
            'journal_entries' => 'table',
            'journal_entry_lines' => 'table',
            'accounts_payable_invoices' => 'table',
            'accounts_payable_payments' => 'table',
            'ap_invoice_payments' => 'table',
            'accounts_receivable_invoices' => 'table',
            'accounts_receivable_receipts' => 'table',
            'ar_invoice_receipts' => 'table',
            'budgets' => 'table',
            'budget_lines' => 'table',
            'tax_transactions' => 'table',
            'accounting_audit_trails' => 'table',
        ];

        $this->info('🔍 Checking existing tables:');
        $this->newLine();

        $existing = 0;
        $missing = 0;

        foreach ($tables as $table => $type) {
            if (Schema::hasTable($table)) {
                $this->line("✅ <info>$table</info> exists");
                $existing++;
            } else {
                $this->line("❌ <comment>$table</comment> not found");
                $missing++;
            }
        }

        $this->newLine();
        $this->info("Summary: $existing exist, $missing missing");
        $this->newLine();
        $this->info('Run: php artisan migrate:mark-complete (without --check) to mark existing tables');

        return 0;
    }
}
