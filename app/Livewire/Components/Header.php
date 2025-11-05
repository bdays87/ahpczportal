<?php

namespace App\Livewire\Components;

use App\Interfaces\icurrencyInterface;
use Livewire\Component;

class Header extends Component
{
    protected $currencyrepository;
    public function boot(icurrencyInterface $currencyrepository){
        $this->currencyrepository = $currencyrepository;
    }
    public function getcurrencies(){
        return $this->currencyrepository->getall("active");
    }
    public function render()
    {
        return view('livewire.components.header',[
            'currencies'=>$this->getcurrencies()
        ]);
    }
}
