<?php

namespace App\Jobs;

use App\Interfaces\ismsbroadcastInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendBulkSmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Seconds the job may run before timing out.
     */
    public $timeout = 600;

    /**
     * Number of attempts before the job is marked failed.
     */
    public $tries = 3;

    /**
     * @param  array  $messages  [['phone' => '..', 'message' => '..'], ...]
     */
    public function __construct(
        public array $messages
    ) {}

    public function handle(ismsbroadcastInterface $smsRepo): void
    {
        $smsRepo->sendBatchSms($this->messages);
    }
}
