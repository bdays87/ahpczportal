<?php

namespace App\Interfaces;

interface isystemmoduleInterface
{
    public function getAll();
    public function getmenu();
    public function getfullmenubyaccounttype($id);
    public function create($data);
    public function get($id);
    public function getSubmodules($id);
    public function update($id, $data);
    public function delete($id);
}
