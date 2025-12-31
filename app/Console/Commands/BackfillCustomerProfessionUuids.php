<?php

namespace App\Console\Commands;

use App\Models\Customerprofession;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class BackfillCustomerProfessionUuids extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:backfill-customer-profession-uuids';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backfill UUIDs for Customerprofession records that are missing UUIDs';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $customerprofessions = Customerprofession::whereNull('uuid')
            ->orWhere('uuid', '')
            ->get();

        if ($customerprofessions->isEmpty()) {
            $this->info('No Customerprofession records found without UUIDs.');

            return Command::SUCCESS;
        }

        $this->info("Found {$customerprofessions->count()} Customerprofession records without UUIDs.");

        $count = 0;
        $bar = $this->output->createProgressBar($customerprofessions->count());
        $bar->start();

        foreach ($customerprofessions as $customerprofession) {
            $customerprofession->update([
                'uuid' => Str::uuid()->toString(),
            ]);
            $count++;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Successfully backfilled UUIDs for {$count} Customerprofession records.");

        return Command::SUCCESS;
    }
}
