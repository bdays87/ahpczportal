<?php

namespace App\Interfaces;

interface idashboardInterface
{
    public function getApplicationStatistics($year = null);

    public function getRegistrationStatistics($year = null);

    public function getInvoiceStatistics($year = null);

    public function getRecentApplications($limit = 10);

    public function getApplicationsByProfession($year = null);

    public function getApplicationsByRegistertype($year = null);

    public function getApplicationsByCustomertype($year = null);
}
