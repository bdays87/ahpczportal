<?php

namespace App\Livewire;

use App\Interfaces\ipaynowInterface;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Checktransactions extends Component
{
    public $uuid;
    public bool $loader = true;
    protected $paynowrepository;
    public $successmessage;
    public $errormessage;
    public function boot(ipaynowInterface $paynowrepository){
        $this->paynowrepository = $paynowrepository;
    }
    public function mount($uuid){
        $this->uuid = $uuid;
        $this->loader = true;
        $this->checktransaction();
    }
    public function checktransaction(){
        $this->loader = true;
        $response = $this->paynowrepository->checktransaction($this->uuid);
      
        if($response['status']=='success'){
            $this->successmessage = $response['message'];
            $this->dispatch('wallet_refresh');
        }else{
            $this->errormessage = $response['message'];
        }
        $this->loader = false;
    }
    #[Layout('components.layouts.plain')]
    public function render()
    {
        return view('livewire.checktransactions');
    }
}
