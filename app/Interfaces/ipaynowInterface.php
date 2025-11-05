<?php

namespace App\Interfaces;

interface ipaynowInterface
{
    public function gettransactions($customer_id);
    public function initiatetransaction(array $data);
    public function checktransaction($uuid);
    public function checktransactionbyid($id);
}
