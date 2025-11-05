<?php

namespace App\implementations;

use App\Interfaces\iexchangerateInterface;
use App\Models\Exchangerate;
use Illuminate\Support\Facades\Auth;

class _exchangerateRepository implements iexchangerateInterface
{
    /**
     * Create a new class instance.
     */
    protected $model;
    public function __construct(Exchangerate $model)
    {
        $this->model = $model;
    }
    public function getAll($year)
    {
        return $this->model->with('basecurrency', 'secondarycurrency', 'user')->whereYear('created_at', $year)->get();
    }
    public function get($id)
    {
        return $this->model->find($id);
    }
    public function create($data)
    {
        try {
            $data['user_id'] = Auth::user()->id;
           $this->model->create($data);
           return ["status"=>"success", "message"=>"Exchangerate created successfully"];
        } catch (\Exception $e) {
            return ["status"=>"error", "message"=>$e->getMessage()];
        }
    }
    public function update($id, $data)
    {
        try {
            $this->model->find($id)->update($data);
            return ["status"=>"success", "message"=>"Exchangerate updated successfully"];
        } catch (\Exception $e) {
            return ["status"=>"error", "message"=>$e->getMessage()];
        }
    }
    public function delete($id)
    {
        try {
            $rate = $this->model->where('id', $id)->first();
            if($rate){
                $rate->delete();
                return ["status"=>"success", "message"=>"Exchangerate deleted successfully"];
            }
            return ["status"=>"error", "message"=>"Exchangerate not found"];
        } catch (\Exception $e) {
            return ["status"=>"error", "message"=>$e->getMessage()];
        }
    }
    public function getrate($currency_id,$year)
    {
        return $this->model->with('basecurrency', 'secondarycurrency')->where('secondary_currency_id', $currency_id)->whereYear('created_at', $year)->first();
    }
    public function getlatestrate($currency_id)
    {
        return $this->model->with('basecurrency', 'secondarycurrency')->where('secondary_currency_id', $currency_id)->latest()->first();
    }
}
