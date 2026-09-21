<?php

namespace App\Interfaces;

interface ibudgetInterface
{
    public function getAll($year = null, $status = null);
    public function get($id);
    public function create($data, $lines);
    public function update($id, $data, $lines);
    public function delete($id);
    public function approve($id);
    public function getBudgetVsActual($id);
}
