<?php

namespace App\Interfaces;

interface iotherapplicationInterface
{
    public  function getbycustomer($customer_id,$year);

    public function getbyuuid($uuid);
    public function getotherapplications($search,$status,$year);

    public function getvalidinstitutions($search = null, $service = null, $province_id = null, $practitioner = null);
    public function getserviceoptions();
    public function makedecision($uuid,$status,$comment=null);
    public  function getbyid($id);
    public  function create($data);
    public  function update($id, $data);
    public  function delete($id);
    public  function getdocuments($id);
    public  function createdocument($data);
    public  function deletedocument($id);
    public  function verifydocument($id,$data);

    public function addinstservice($data);
    public function removeinstservice($id);
    public function addinstcustomer($data);
    public function removeinstcustomer($id);
    public function searchcustomers($search);
}
