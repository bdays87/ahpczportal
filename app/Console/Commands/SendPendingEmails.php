<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SendPendingEmails extends Command
{
    /**
     * Drains the queued email/SMS jobs and then stops. Safe to run from the
     * admin UI button, a cron schedule, or the terminal.
     */
    protected $signature = 'app:send-pending-emails {--max-time=30}';

    protected $description = 'Process queued email/SMS broadcast jobs, then stop when the queue is empty.';

    public function handle(): int
    {
        $this->call('queue:work', [
            '--stop-when-empty' => true,
            '--max-time' => (int) $this->option('max-time'),
            '--tries' => 3,
            '--no-interaction' => true,
        ]);

        return self::SUCCESS;
    }
}
