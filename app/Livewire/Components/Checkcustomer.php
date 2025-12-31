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
use Livewire\WithFileUploads;
class Checkcustomer extends Component
{
    use Toast,WithFileUploads;
    public $search;
    public $profile =null;
    public $name;
    public $email;
    public $phone;
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
    public $signup_type;
    public $registration_number;
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
        }elseif(Auth::user()->customer->customer->profile_complete==0){
           
            $this->modal = true; 
            $this->name = Auth::user()->name;
            $this->surname = Auth::user()->surname;
            $this->identitynumber = Auth::user()->customer->customer->identificationnumber;
            $this->identitytype = Auth::user()->customer->customer->identificationtype;
            $this->dob = Auth::user()->customer->customer->dob;
            $this->gender = Auth::user()->customer->customer->gender;
            $this->maritalstatus = Auth::user()->customer->customer->maritalstatus;
            $this->previousname = Auth::user()->customer->customer->previous_name;
            $this->nationality_id = Auth::user()->customer->customer->nationality_id;
            $this->province_id = Auth::user()->customer->customer->province_id;
            $this->city_id = Auth::user()->customer->customer->city_id;
            $this->address = Auth::user()->customer->customer->address;
            $this->placeofbirth = Auth::user()->customer->customer->place_of_birth;
            $this->email = Auth::user()->email;
            $this->phone = Auth::user()->phone;
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
            'maritalstatus'=>'required',
            'registration_number'=>'required_if:signup_type,1',
            
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
        if(Auth::user()->customer==null){
            if($this->profile){
                $this->validate([
                    'profile'=>'required|image|mimes:jpeg,png,jpg,gif|max:2048'
                ]);
                $this->profile = $this->profile->store('customers','public');
            }
        $response = $this->customerrepo->register([
            'name'=>$this->name,
            'surname'=>$this->surname,
            'nationality_id'=>$this->nationality_id,
            'province_id'=>$this->province_id,
            'city_id'=>$this->city_id,
            'address'=>$this->address,
            'email'=>$this->email,
            'phone'=>$this->phone,
            'place_of_birth'=>$this->placeofbirth,
            'identificationnumber'=>$this->identitynumber,
            'identificationtype'=>$this->identitytype,
            'dob'=>$this->dob,
            'gender'=>$this->gender,
            'maritalstatus'=>$this->maritalstatus,
            'previous_name'=>$this->previousname,
            'profile'=>$this->profile,
            'signup_type'=>$this->signup_type,
            'registration_number'=>$this->registration_number,
        ]);
        }else{
            $response = $this->customerrepo->update(Auth::user()->customer->customer->id,[
                'name'=>$this->name,
            'surname'=>$this->surname,
            'email'=>$this->email,
            'phone'=>$this->phone,
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
            'profile'=>$this->profile,
            ]);
        }
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
