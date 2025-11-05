<?php

namespace App\Interfaces;

interface iaccounttypeInterface
{
    public function getAll();
    public function get($id);
    public function getsystemmodules($id);
    public function create($data);
    public function update($id, $data);
    public function delete($id);
}
