<?php

namespace App\Interfaces;

interface inewsletterInterface
{
    public function getAll($year = null);

    public function getLatest($limit = 5);

    public function get($id);

    public function create(array $data);

    public function update(int $id, array $data);

    public function delete(int $id);

    public function broadcast(int $id);
}
