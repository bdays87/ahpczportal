<?php

namespace App\Interfaces;

interface iapinvoiceInterface
{
    public function getAll($search = null, $status = null, $supplierId = null);
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
