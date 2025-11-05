<?php

namespace App\Interfaces;

interface ipaymentchannelInterface
{
    public function getAll();
    public function get($id);
    public function create($data);
    public function update($id, $data);
    public function delete($id);

    public function getparameters($id);
    public function getparameter($id);
    public function getparameterbycurrency($id,$currency_id);
    public function createparameter($data);
    public function updateparameter($id, $data);
    public function deleteparameter($id);
    public function getchannelparameters($channel,$currency_id);
}
