<?php

namespace App\Interfaces;

interface iapplicationsessionInterface
{
    public function create($data);
    public function update($id,$data);
    public function delete($id);
    public function get($id);
    public function getall();
    public function getactive();
}
