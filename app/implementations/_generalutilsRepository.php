<?php

namespace App\implementations;

use App\Interfaces\igeneralutilsInterface;
use App\Models\Registrationnumber;

class _generalutilsRepository implements igeneralutilsInterface
{
    /**
     * Create a new class instance.
     */
    protected $registrationnumber;
    public function __construct(Registrationnumber $registrationnumber)
    {
        $this->registrationnumber = $registrationnumber;
    }
    public function generateregistrationnumber(){
        try{
            $registrationnumber = $this->registrationnumber->where('year', date('Y'))->first();
            $number=0;
            if($registrationnumber == null){
                $registrationnumber = $this->registrationnumber->create([
                    'year'=>date('Y'),
                    'number'=>1
                ]);
                $number=1;
            }else{
                $number=$registrationnumber->number+1;
                $registrationnumber->update([
                    'number'=>$number
                ]);
            }
            $generatednumber = config('generalutils.registration_prefix').$registrationnumber->year.$number;
            return ["status"=>"success","message"=>"","data"=>$generatednumber];
        }catch(\Throwable $e){
            return ["status"=>"error","message"=>$e->getMessage()];
        }
    }
    public function getregistrationnumber(){
        try{
            
        }catch(\Throwable $e){
            return ["status"=>"error","message"=>$e->getMessage()];
        }
    }
    public function generateinvoice($id){
        $year = date('Y');
        $randomnumber = rand(1000,9999);
        return "INV-".$year."-".$randomnumber."-".$id;
        
    }
    public function generatereceiptnumber($id){
        $year = date('Y');
        $randomnumber = rand(1000,9999);
        return "REC-".$year."-".$randomnumber."-".$id;
    }
    public function generatecertificatenumber($prefix,$id){
        $year = date('Y');
        $randomnumber = rand(1000,9999);
        return $prefix."-".$year."-".$randomnumber."-".$id;
    }
}
