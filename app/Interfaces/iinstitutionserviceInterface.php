<?php

namespace App\Interfaces;

interface iinstitutionserviceInterface
{
    public function getAll();
    public function getActive();
    public function getByInstitution($institution_id);
    public function get($id);
    public function create($data);
    public function update($id, $data);
    public function delete($id);
}
