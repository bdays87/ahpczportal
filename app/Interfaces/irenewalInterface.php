<?php

namespace App\Interfaces;

interface irenewalInterface
{
    public function getfees();

    public function getfee($id);
    public function createfee(array $data);
    public function updatefee($id,array $data);

    public function deletefee($id);

    public function getdocuments();
    public function getdocument($id);
    public function createdocument(array $data);
    public function updatedocument($id,array $data);
    public function deletedocument($id);
}
