<?php

namespace App\Interfaces;

interface isuspenseInterface
{
    public function getstatement($customer_id);
    public function getbalances($customer_id);
    public function getbalance($customer_id, $currency_id);
    public function createSuspense(array $data);
    public function deleteSuspense($id);
}
