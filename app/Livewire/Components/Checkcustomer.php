<?php

namespace App\Livewire\Components;

use App\Interfaces\icityInterface;
use App\Interfaces\icustomerInterface;
use App\Interfaces\iemploymentlocationInterface;
use App\Interfaces\iemploymentstatusInterface;
use App\Interfaces\inationalityInterface;
use App\Interfaces\iprovinceInterface;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Mary\Traits\Toast;

class Checkcustomer extends Component
{
    use Toast;
    public $search;
    public $profile =null;
    public $name;
    public $surname;
    public $nationalid;
    public $previousname;
    public $dob;
    public $gender;
    public $maritalstatus;
    public $identitytype;
    public $identitynumber;
    public $nationality_id;
    public $employmentstatus_id;
    public $employmentlocation_id;
    public $province_id;
    public $city_id;
    public $customertype_id;
    public $address;
    public $placeofbirth;
    public $id;
    protected $customerrepo;
    protected $nationalityrepo;
    protected $provincerepo;
    protected $cityrepo;
    protected $employmentstatusrepo;
    protected $employmentlocationrepo;
    public $modal = false;
    public  function mount(){
        if(Auth::user()->customer==null){
            $this->modal = true; 
            $this->name = Auth::user()->name;
            $this->surname = Auth::user()->surname;
        }
    }
    public function boot(icustomerInterface $customerrepo, iemploymentlocationInterface $employmentlocationrepo, inationalityInterface $nationalityrepo, iprovinceInterface $provincerepo, icityInterface $cityrepo, iemploymentstatusInterface $employmentstatusrepo){
        $this->customerrepo = $customerrepo;
        $this->employmentlocationrepo = $employmentlocationrepo;
        $this->nationalityrepo = $nationalityrepo;
        $this->provincerepo = $provincerepo;
        $this->cityrepo = $cityrepo;
        $this->employmentstatusrepo = $employmentstatusrepo;
    }

    public function getnationalities(){
        return $this->nationalityrepo->getAll(null);
    }

    public function getemploymentlocations(){
        return $this->employmentlocationrepo->getAll();
    }

    public function getprovinces(){
        return $this->provincerepo->getAll();
    }

    public function getcities(){
        return $this->cityrepo->getAll();
    }

    public function getemploymentstatuses(){
        return $this->employmentstatusrepo->getAll();
    }

    public function register(){
        $this->validate([
            'name'=>'required',
            'surname'=>'required',
            'nationality_id'=>'required',
            'address'=>'required',
            'placeofbirth'=>'required',
            'identitynumber'=>'required',
            'identitytype'=>'required',
            'dob'=>'required|date',
            'gender'=>'required',
            'maritalstatus'=>'required'
            
        ]);
     
        if($this->nationality_id==1){
            $this->validate([
                'province_id'=>'required',
                'city_id'=>'required'
            ]);
            if($this->identitytype=='NATIONAL_ID'){
           
                $result = preg_match("/[0-9]{8,9}[a-z,A-Z][0-9]{2}/i", $this->identitynumber);
                if ($result == 0) {
                   $this->addError("identitynumber", "Required formate 00000000L00");
                   return;
                }
            }
             
        }

        $response = $this->customerrepo->register([
            'name'=>$this->name,
            'surname'=>$this->surname,
            'nationality_id'=>$this->nationality_id,
            'province_id'=>$this->province_id,
            'city_id'=>$this->city_id,
            'address'=>$this->address,
            'place_of_birth'=>$this->placeofbirth,
            'identificationnumber'=>$this->identitynumber,
            'identificationtype'=>$this->identitytype,
            'dob'=>$this->dob,
            'gender'=>$this->gender,
            'maritalstatus'=>$this->maritalstatus,
            'previous_name'=>$this->previousname,
        ]);
        if($response['status']=='success'){
            $this->modal = false;
            $this->success($response['message']);
            $this->dispatch('customer_refresh');
        }else{
            $this->error($response['message']);
        }
    }

    public function render()
    {
        return view('livewire.components.checkcustomer',[
            'nationalities'=>$this->getnationalities(),
            'provinces'=>$this->getprovinces(),
            'cities'=>$this->getcities(),
            'employmentstatuses'=>$this->getemploymentstatuses(),
            'employmentlocations'=>$this->getemploymentlocations()
        ]);
    }
}
