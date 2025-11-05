<?php

namespace App\Interfaces;

interface icertificatetypeInterface
{
    public function getAll();
    public function create($data);
    public function update($id, $data);
    public function delete($id);
}
