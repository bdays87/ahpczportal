<?php

namespace App\Interfaces;

interface ipenaltyperiodInterface
{
    public function getAll();
    public function getActive();
    public function create($data);
    public function update($id, $data);
    public function delete($id);
    public function get($id);
}
