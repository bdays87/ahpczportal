<?php

namespace App\Interfaces;

interface icustomeremploymentInteface
{
    public function create($data);
    public function get($id);
    public function update($id, $data);
    public function delete($id);
}
