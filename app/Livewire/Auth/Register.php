<?php

namespace App\Livewire\Auth;

use App\Interfaces\iaccounttypeInterface;
use App\Interfaces\icityInterface;
use App\Interfaces\icustomerInterface;
use App\Interfaces\iemploymentlocationInterface;
use App\Interfaces\iemploymentstatusInterface;
use App\Interfaces\inationalityInterface;
use App\Interfaces\iprovinceInterface;
use App\Interfaces\iuserInterface;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Mary\Traits\Toast;

class Register extends Component
{
    use Toast;
    public $name;
    public $surname;
    public $email;
    public $phone;
    public $password;
    public $password_confirmation;
    public $accounttype_id;
    public $signup_type;
    public $registration_number;
    protected $accounttyperepo;
    protected  $userrepo;
    public function boot(iaccounttypeInterface $accounttyperepo, iuserInterface $userrepo){
        $this->accounttyperepo = $accounttyperepo;
        $this->userrepo = $userrepo;
    }
    
    public function getaccounttypes(){
        return $this->accounttyperepo->getAll(null)->where('id', '!=', 1);
    }

  

    public function register(){
        $this->validate([
            'name'=>'required',
            'surname'=>'required',
            'email'=>'required|email',
            'phone'=>'required',
            'password'=>'required|min:8|confirmed',
            'accounttype_id'=>'required'
        ]);
        $data = [
            'name'=>$this->name,
            'surname'=>$this->surname,
            'email'=>$this->email,
            'phone'=>$this->phone,
            'password'=>$this->password,
            'accounttype_id'=>$this->accounttype_id,
        ];
        $response =$this->userrepo->register($data);
        if($response['status'] == 'success'){
            $this->success('User registered successfully');
            $this->redirect(route('login'));
        }else{
            $this->error($response['message']);
        }
      
        
    }

    #[Layout('components.layouts.plain')]
    public function render()
    {
        return view('livewire.auth.register',[
            'accounttypes'=>$this->getaccounttypes()
        ]);
    }
}
