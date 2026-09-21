<?php

namespace App\Interfaces;

interface iaudittrailInterface
{
    public function getAll($module = null, $from = null, $to = null);
    public function log($module, $action, $recordType, $recordId, $reference, $oldValues, $newValues);
}
