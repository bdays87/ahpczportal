<?php

namespace App\Interfaces;

interface itaxrateInterface
{
    public function getAll($status = null);
    public function get($id);
    public function create($data);
    public function update($id, $data);
    public function delete($id);
    public function getTaxReport($periodId = null, $from = null, $to = null);
}
