<?php

namespace App\Jobs;

use App\Interfaces\iemailbroadcastInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendEmailCampaignJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 1800;

    public $tries = 2;

    public function __construct(public int $campaignId) {}

    public function handle(iemailbroadcastInterface $broadcastRepo): void
    {
        $broadcastRepo->sendBroadcast($this->campaignId);
    }
}
