<?php

namespace App\Interfaces;

interface iemailbroadcastInterface
{
    public function addCredits(array $data);

    public function getTotalCredits();

    public function getUsedCredits();

    public function getRemainingCredits();

    public function getCreditHistory();

    public function createCampaign(array $data);

    public function getCampaigns();

    public function getCampaignById($id);

    public function getFilteredRecipients(array $filters);

    public function sendBroadcast($campaignId);

    public function getCampaignStatistics($campaignId);

    public function sendSingleEmail($to, $subject, $message, $attachments = null);

    /**
     * Send a personalized email to many recipients efficiently.
     * Each recipient: ['email' => string, 'tokens' => ['{name}' => 'John', ...]].
     */
    public function sendBatchEmail(array $recipients, $subject, $bodyTemplate);
}




