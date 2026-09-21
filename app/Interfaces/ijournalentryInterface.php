<?php

namespace App\Interfaces;

interface ijournalentryInterface
{
    public function getAll($search = null, $status = null, $periodId = null);
    public function get($id);
    public function create($data, $lines);
    public function update($id, $data, $lines);
    public function delete($id);
    public function post($id);
    public function reverse($id, $reason);
    public function generateReference();
}
