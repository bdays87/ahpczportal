<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class FixAccountingMigrations extends Command
{
    protected $signature = 'migrate:fix-accounting';
    protected $description = 'Fix accounting migrations by adding Schema::hasTable checks';

    public function handle()
    {
        $migrations = [
            '2026_09_01_000001_create_accounting_periods_table.php' => 'accounting_periods',
            '2026_09_01_000002_create_cost_centers_table.php' => 'cost_centers',
            '2026_09_01_000004_create_tax_rates_table.php' => 'tax_rates',
            '2026_09_01_000005_create_suppliers_table.php' => 'suppliers',
            '2026_09_01_000006_create_journal_entries_table.php' => 'journal_entries',
            '2026_09_01_000007_create_journal_entry_lines_table.php' => 'journal_entry_lines',
            '2026_09_01_000008_create_accounts_payable_invoices_table.php' => 'accounts_payable_invoices',
            '2026_09_01_000009_create_accounts_payable_payments_table.php' => 'accounts_payable_payments',
            '2026_09_01_000010_create_ap_invoice_payments_table.php' => 'ap_invoice_payments',
            '2026_09_01_000011_create_accounts_receivable_invoices_table.php' => 'accounts_receivable_invoices',
            '2026_09_01_000012_create_accounts_receivable_receipts_table.php' => 'accounts_receivable_receipts',
            '2026_09_01_000013_create_ar_invoice_receipts_table.php' => 'ar_invoice_receipts',
            '2026_09_01_000014_create_budgets_table.php' => 'budgets',
            '2026_09_01_000015_create_budget_lines_table.php' => 'budget_lines',
            '2026_09_01_000016_create_tax_transactions_table.php' => 'tax_transactions',
            '2026_09_01_000017_create_accounting_audit_trail_table.php' => 'accounting_audit_trails',
        ];

        $fixed = 0;
        $skipped = 0;

        foreach ($migrations as $file => $tableName) {
            $path = database_path("migrations/$file");

            if (!File::exists($path)) {
                $this->warn("File not found: $file");
                continue;
            }

            $content = File::get($path);

            // Skip if already fixed
            if (str_contains($content, 'Schema::hasTable')) {
                $this->info("⏭️  Skipped (already fixed): $file");
                $skipped++;
                continue;
            }

            // Add hasTable check
            $newContent = preg_replace(
                "/(    public function up\(\): void\s+\{\s+)(Schema::create\('$tableName')/s",
                "$1if (!Schema::hasTable('$tableName')) {\n            $2",
                $content
            );

            // Close the if statement before the closing of up()
            $newContent = preg_replace(
                "/(        \}\);\s+)(    \}\s+public function down)/s",
                "$1    }\n$2",
                $newContent
            );

            File::put($path, $newContent);
            $this->info("✅ Fixed: $file");
            $fixed++;
        }

        $this->newLine();
        $this->info("====================================");
        $this->info("✅ Fixed: $fixed");
        $this->info("⏭️  Skipped: $skipped");
        $this->info("====================================");

        if ($fixed > 0) {
            $this->newLine();
            $this->info("🎉 All migrations fixed! Now run: php artisan migrate");
        }

        return 0;
    }
}
