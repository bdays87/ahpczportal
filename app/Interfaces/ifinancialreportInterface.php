<?php

namespace App\Interfaces;

interface ifinancialreportInterface
{
    public function getTrialBalance($periodId = null, $from = null, $to = null);
    public function getProfitAndLoss($periodId = null, $from = null, $to = null);
    public function getBalanceSheet($periodId = null, $asAt = null);
    public function getCashFlow($periodId = null, $from = null, $to = null);
    public function getGeneralLedger($accountId = null, $from = null, $to = null);
}
