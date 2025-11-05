<?php

namespace App\Interfaces;

interface inationalityInterface
{
    public function getAll($search);
    public function get($id);
    public function create($data);
    public function update($id, $data);
    public function delete($id);
}
