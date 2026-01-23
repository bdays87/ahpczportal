<?php

namespace App\Console\Commands;

use App\Models\Journal;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class GenerateJournalSlugs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'journals:generate-slugs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate slugs for existing journals that are missing slugs';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $journals = Journal::whereNull('slug')
            ->orWhere('slug', '')
            ->get();

        if ($journals->isEmpty()) {
            $this->info('No journals found without slugs.');

            return Command::SUCCESS;
        }

        $this->info("Found {$journals->count()} journals without slugs.");

        $count = 0;
        $bar = $this->output->createProgressBar($journals->count());
        $bar->start();

        foreach ($journals as $journal) {
            $baseSlug = Str::slug($journal->title);
            $slug = $baseSlug;
            $counter = 1;

            // Ensure unique slug
            while (Journal::where('slug', $slug)->where('id', '!=', $journal->id)->exists()) {
                $slug = $baseSlug.'-'.$counter;
                $counter++;
            }

            $journal->update(['slug' => $slug]);
            $count++;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Successfully generated slugs for {$count} journals.");

        return Command::SUCCESS;
    }
}
