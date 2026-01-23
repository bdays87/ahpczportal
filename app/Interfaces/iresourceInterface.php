<?php

namespace App\Interfaces;

interface iresourceInterface
{
    public function getAll($search);

    public function get($id);

    public function create($data);

    public function update($id, $data);

    public function delete($id);

    public function getActiveResources($search);

    public function getLatest($limit);
}
