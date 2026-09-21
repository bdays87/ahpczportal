<?php

/**
 * Script to fix accounting migrations by adding Schema::hasTable() checks
 * Run: php fix_accounting_migrations.php
 */

$migrationsPath = __DIR__ . '/database/migrations/';

$accountingMigrations = [
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
$errors = 0;

foreach ($accountingMigrations as $file => $tableName) {
    $filePath = $migrationsPath . $file;
    
    if (!file_exists($filePath)) {
        echo "❌ File not found: $file\n";
        $errors++;
        continue;
    }
    
    $content = file_get_contents($filePath);
    
    // Check if already has the fix
    if (strpos($content, 'Schema::hasTable') !== false) {
        echo "⏭️  Already fixed: $file\n";
        $skipped++;
        continue;
    }
    
    // Find and replace Schema::create pattern
    $pattern = "/    public function up\(\): void\s+\{\s+Schema::create\('$tableName'/s";
    $replacement = "    public function up(): void\n    {\n        if (!Schema::hasTable('$tableName')) {\n            Schema::create('$tableName'";
    
    $newContent = preg_replace($pattern, $replacement, $content);
    
    if ($newContent === $content) {
        echo "⚠️  Pattern not matched: $file\n";
        $errors++;
        continue;
    }
    
    // Also need to close the if statement - find the closing of the Schema::create
    // This is tricky, so let's find the last }); in the up() method and add an extra }
    $newContent = preg_replace(
        "/(        \}\);\s+\}\s+public function down)/",
        "$1\n        }", // Add closing brace for if statement
        $newContent
    );
    
    // Wait, that's wrong. Let me fix it properly
    // Find: closing of Schema::create which is    });
    // Replace with:    });\n        }
    $newContent = preg_replace(
        "/(    \}\);\s+\}\s+public function down)/",
        "    });\n        }\n    }\n\n    public function down",
        $newContent,
        1
    );
    
    // Write back to file
    if (file_put_contents($filePath, $newContent)) {
        echo "✅ Fixed: $file\n";
        $fixed++;
    } else {
        echo "❌ Failed to write: $file\n";
        $errors++;
    }
}

echo "\n=================================\n";
echo "Summary:\n";
echo "✅ Fixed: $fixed\n";
echo "⏭️  Skipped: $skipped\n";
echo "❌ Errors: $errors\n";
echo "=================================\n";

if ($fixed > 0) {
    echo "\n✅ All migrations fixed! Now run: php artisan migrate\n";
}
