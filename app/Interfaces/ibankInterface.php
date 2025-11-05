<?php

namespace App\Interfaces;

interface ibankInterface
{
    public function getAll($search);
    public function get($id);
    public function create($data);
    public function update($id, $data);
    public function delete($id);

    public function getaccounts($bank_id);
    public function getaccount($id);
    public function createaccount($data);
    public function updateaccount($id, $data);
    public function deleteaccount($id);
}
