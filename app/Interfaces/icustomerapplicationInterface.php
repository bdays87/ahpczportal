<?php

namespace App\Interfaces;

interface icustomerapplicationInterface
{
    public function retrieve($year, $status, $search = null, $applicationtype_id = null);

    public function getbyuuid($uuid);

    public function makedecision($data);

    public function reportData(array $filters = []);

    public function compliancereportData(array $filters = []);
}
