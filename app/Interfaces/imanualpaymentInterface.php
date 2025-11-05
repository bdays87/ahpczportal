<?php

namespace App\Interfaces;

interface imanualpaymentInterface
{
    public function create($data);
    public function delete($id);
    public function getAll($year);
    public function getbycustomer($customer_id);
}
