<?php

namespace App\Jobs;

use App\Interfaces\iemailbroadcastInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendBulkEmailJob implements ShouldQueue
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
     * @param  array  $recipients  [['email' => '..', 'tokens' => ['{name}' => '..']], ...]
     */
    public function __construct(
        public array $recipients,
        public string $subject,
        public string $bodyTemplate,
        public ?string $provider = null
    ) {}

    public function handle(iemailbroadcastInterface $emailRepo): void
    {
        $emailRepo->sendBatchEmail($this->recipients, $this->subject, $this->bodyTemplate, $this->provider);
    }
}
