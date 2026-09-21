<?php

namespace App\Interfaces;

interface iaccountingperiodInterface
{
    public function getAll();
    public function getOpen();
    public function get($id);
    public function create($data);
    public function update($id, $data);
    public function delete($id);
    public function close($id);
    public function lock($id);
    public function getCurrentPeriod();
    public function getPeriodByMonthYear($month, $year);
}
