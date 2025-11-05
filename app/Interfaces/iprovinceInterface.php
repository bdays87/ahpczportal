<?php

namespace App\Interfaces;

interface iprovinceInterface
{
    public function getAll();
    public function get($id);
    public function create($data);
    public function update($id, $data);
    public function delete($id);
}
