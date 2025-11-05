<?php

namespace App\Interfaces;

interface icustomertypeInterface
{
    public function getAll();
    public function get($id);
    public function create($data);
    public function update($id, $data);
    public function delete($id);
    public function assignregistertype($id, $registertype_id);
    public function removeregistertype($id, $registertype_id);
    public function getregistertypes($id);
}
