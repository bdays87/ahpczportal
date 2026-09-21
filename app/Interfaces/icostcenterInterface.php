<?php

namespace App\Interfaces;

interface icostcenterInterface
{
    public function getAll($status = null);
    public function get($id);
    public function create($data);
    public function update($id, $data);
    public function delete($id);
    public function getTree();
}
