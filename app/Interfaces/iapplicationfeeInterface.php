<?php

namespace App\Interfaces;

interface iapplicationfeeInterface
{
    public function getAll($year);
    public function create($data);
    public function update($id, $data);
    public function delete($id);
    public function get($id);
}
