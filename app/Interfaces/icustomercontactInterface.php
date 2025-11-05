<?php

namespace App\Interfaces;

interface icustomercontactInterface
{
    public function create($data);
    public function get($id);
    public function update($id, $data);
    public function delete($id);
}
