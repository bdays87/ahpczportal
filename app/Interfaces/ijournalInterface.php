<?php

namespace App\Interfaces;

interface ijournalInterface
{
    public function getAll($search = null);

    public function getLatest($limit = 5);

    public function get($id);

    public function getBySlug($slug);

    public function create(array $data);

    public function update(int $id, array $data);

    public function delete(int $id);
}
