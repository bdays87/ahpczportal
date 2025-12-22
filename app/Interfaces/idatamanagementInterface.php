<?php

namespace App\Interfaces;

interface idatamanagementInterface
{
    public function getprofessionimports($search);
    public function saveprofessionimport($file);
    public function getprofessionimport($id);
    public function createprofession($data);
    public function updateprofessionimport($id, $data);
    public function deleteprofessionimport($id);

    public function importcustomers($file);
    public function getallcustomers($search=null);
    public function getcustomer($id);
    public function createcustomer($data);
    public function updatecustomer($id, $data);
    public function deletecustomer($id);
    public function getallusers($search=null);
    public function importusers($file);
    public function getuser($id);
    public function createuser($data);
    public function updateuser($id, $data);
    public function deleteuser($id);

    public function importcustomerprofessions($file);
    public function getallcustomerprofessions($search=null);
    public function getcustomerprofession($id);
    public function createcustomerprofession($data);
    public function updatecustomerprofession($id, $data);
    public function deletecustomerprofession($id);

    public function importcustomerregistrations($file);
    public function getallcustomerregistrationimports($search=null);
    public function getcustomerregistrationimport($id);
    public function createcustomerregistrationimport($data);
    public function updatecustomerregistrationimport($id, $data);
    public function deletecustomerregistrationimport($id);
    public function importcustomerapplications($file);
    public function getallcustomerapplicationimports($search=null);
    public function getcustomerapplicationimport($id);
    public function createcustomerapplicationimport($data);
    public function updatecustomerapplicationimport($id, $data);
    public function deletecustomerapplicationimport($id);

    public function importcustomercdps($file);
    public function getallcustomercdps($search=null);
    public function getcustomercdp($id);
    public function createcustomercdp($data);
    public function updatecustomercdp($id, $data);
    public function deletecustomercdp($id);
}
