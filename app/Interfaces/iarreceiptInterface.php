<?php

namespace App\Interfaces;

interface iarreceiptInterface
{
    public function getAll($search = null, $status = null, $customerId = null);
    public function get($id);
    public function create($data, $allocations);
    public function delete($id);
    public function post($id);
    public function cancel($id);
    public function generateReceiptNumber();
}
