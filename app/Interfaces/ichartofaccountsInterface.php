<?php

namespace App\Interfaces;

interface ichartofaccountsInterface
{
    public function getAll($search = null, $type = null, $status = null);
    public function get($id);
    public function create($data);
    public function update($id, $data);
    public function delete($id);
    public function getByType($type);
    public function getPostable();
    public function getTree();
    public function getAccountBalance($id, $periodId = null);
    public function getLedger($id, $periodId = null, $from = null, $to = null);
}
