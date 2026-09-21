<?php

namespace App\Console\Commands;

use App\Services\BankStatementParser;
use Illuminate\Console\Command;

class TestBankStatementParser extends Command
{
    protected $signature = 'bank:test-parser {file?} {--bank=Generic}';
    protected $description = 'Test bank statement parser with a sample file';

    public function handle()
    {
        $file = $this->argument('file') ?? public_path('imports/NMBstatementformat.csv');
        $bankType = $this->option('bank');

        if (!file_exists($file)) {
            $this->error("File not found: {$file}");
            return 1;
        }

        $this->info("Testing parser for: {$bankType}");
        $this->info("File: {$file}");
        $this->newLine();

        try {
            $parser = new BankStatementParser();
            $result = $parser->parse($file, $bankType);

            // Display metadata
            if (!empty($result['metadata'])) {
                $this->info("=== METADATA ===");
                foreach ($result['metadata'] as $key => $value) {
                    $this->line("  {$key}: {$value}");
                }
                $this->newLine();
            }

            // Display transactions
            $transactions = $result['transactions'];
            $this->info("=== TRANSACTIONS ===");
            $this->line("Found " . count($transactions) . " transactions");
            $this->newLine();

            if (count($transactions) > 0) {
                // Display first 5 transactions
                $this->table(
                    ['Date', 'Description', 'Reference', 'Debit', 'Credit', 'Balance'],
                    collect($transactions)->take(5)->map(fn($t) => [
                        $t['transaction_date'],
                        \Str::limit($t['description'], 30),
                        $t['reference'],
                        $t['debit'] > 0 ? number_format($t['debit'], 2) : '-',
                        $t['credit'] > 0 ? number_format($t['credit'], 2) : '-',
                        number_format($t['balance'], 2),
                    ])->toArray()
                );

                if (count($transactions) > 5) {
                    $this->line("... and " . (count($transactions) - 5) . " more transactions");
                }

                // Statistics
                $this->newLine();
                $this->info("=== STATISTICS ===");
                $totalDebits = collect($transactions)->sum('debit');
                $totalCredits = collect($transactions)->sum('credit');
                $this->line("  Total Debits:  " . number_format($totalDebits, 2));
                $this->line("  Total Credits: " . number_format($totalCredits, 2));
                $this->line("  Net Change:    " . number_format($totalCredits - $totalDebits, 2));
            }

            $this->newLine();
            $this->info("✓ Parser test completed successfully!");
            return 0;

        } catch (\Exception $e) {
            $this->error("✗ Parser failed: " . $e->getMessage());
            $this->error($e->getTraceAsString());
            return 1;
        }
    }
}
