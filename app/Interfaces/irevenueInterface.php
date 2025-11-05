<?php

namespace App\Interfaces;

interface irevenueInterface
{
    public function reportData(array $filters = []);

    public function getSummaryStats(array $filters = []);
}




