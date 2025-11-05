<?php

namespace App\Interfaces;

interface ibanktransactionInterface
{
    public function getAll($search);
    public function search($search);
    public function get($id);
    public function create($data);
    public function update($id, $data);
    public function delete($id);
    public function claim($id,$proofofpayment_id,$customer_id);
    public function importdata($data);
}
