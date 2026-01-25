<?php

namespace App\Interfaces;

interface icustomerhistoricaldataInterface
{
    public function getAll($status = null);

    public function get($id);

    public function approve($id);

    public function reject($id, $reason);

    public function create(array $data);

    public function importFromFile(string $filePath);
}
