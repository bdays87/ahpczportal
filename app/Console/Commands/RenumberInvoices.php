<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RenumberInvoices extends Command
{
    protected $signature = 'invoices:renumber {--dry-run : Show what would change without saving}';

    protected $description = 'Number every invoice INV20261114 + 1, 2, 3... in creation order, and set the counter for new invoices. Keeps a CSV of old -> new numbers';

    public function handle(): int
    {
        $dry = $this->option('dry-run');
        $changes = [];
        $sequence = 0;

        Invoice::orderBy('id')->each(function (Invoice $invoice) use (&$changes, &$sequence) {
            $sequence++;
            $new = Invoice::numberFor($sequence);
            if ($invoice->invoice_number !== $new) {
                $changes[] = [$invoice->id, $invoice->invoice_number, $new, $invoice->sage_invoice_number];
            }
        });

        $this->info("{$sequence} invoice(s), ".count($changes).' need renumbering. Next new invoice: '.Invoice::numberFor($sequence + 1));

        if ($dry) {
            foreach (array_slice($changes, 0, 10) as [$id, $old, $new]) {
                $this->line("  #{$id}: {$old} -> {$new}");
            }

            return self::SUCCESS;
        }

        if (! empty($changes)) {
            // Keep the mapping: old numbers may already be on printed invoices, emails or bank references.
            $file = storage_path('logs/invoice-renumber-'.now()->format('Ymd-His').'.csv');
            $handle = fopen($file, 'w');
            fputcsv($handle, ['id', 'old_invoice_number', 'new_invoice_number', 'sage_invoice_number']);
            foreach ($changes as $row) {
                fputcsv($handle, $row);
            }
            fclose($handle);
            $this->info('Old -> new mapping saved to '.$file);
        }

        DB::transaction(function () use ($changes, $sequence) {
            foreach ($changes as [$id, , $new]) {
                DB::table('invoices')->where('id', $id)->update(['invoice_number' => $new]);
            }
            DB::table('invoice_sequences')->updateOrInsert(
                ['name' => 'invoice'],
                ['last_number' => $sequence, 'updated_at' => now(), 'created_at' => now()]
            );
        });

        $this->info('Done.');

        return self::SUCCESS;
    }
}
