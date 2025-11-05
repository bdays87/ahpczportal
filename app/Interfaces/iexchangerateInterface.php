<?php

namespace App\Interfaces;

interface iexchangerateInterface
{
    public function getAll($year);
    public function get($id);
    public function create($data);
    public function update($id, $data);
    public function delete($id);
    public function getrate($currency_id,$year);
    public function getlatestrate($currency_id);
}
