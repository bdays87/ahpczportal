<?php

namespace App\Interfaces;

interface ismsbroadcastInterface
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
}




