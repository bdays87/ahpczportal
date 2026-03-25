<?php

namespace App\Interfaces;

interface irestorationfeeInterface
{
    public function getAll();
    public function create($data);
    public function update($id, $data);
    public function delete($id);
    public function get($id);
}
