<?php

namespace App\Interfaces;

interface isupplierInterface
{
    public function getAll($search = null, $status = null);
    public function get($id);
    public function create($data);
    public function update($id, $data);
    public function delete($id);
    public function getBalance($id);
    public function getStatement($id, $from = null, $to = null);
}
