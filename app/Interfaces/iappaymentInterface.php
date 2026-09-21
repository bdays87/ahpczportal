<?php

namespace App\Interfaces;

interface iappaymentInterface
{
    public function getAll($search = null, $status = null, $supplierId = null);
    public function get($id);
    public function create($data, $allocations);
    public function delete($id);
    public function post($id);
    public function cancel($id);
    public function generatePaymentNumber();
}
