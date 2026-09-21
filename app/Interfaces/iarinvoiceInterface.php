<?php

namespace App\Interfaces;

interface iarinvoiceInterface
{
    public function getAll($search = null, $status = null, $customerId = null);
    public function get($id);
    public function create($data);
    public function update($id, $data);
    public function delete($id);
    public function post($id);
    public function cancel($id);
    public function getOverdue();
    public function getAgeing();
    public function generateInvoiceNumber();
}
