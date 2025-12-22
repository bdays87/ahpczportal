<?php

namespace App\Livewire;

use Livewire\Component;
use App\Interfaces\ibankInterface;
class Bankdetails extends Component
{
    protected $bankRepository;
    public function boot(ibankInterface $bankRepository)
    {
        $this->bankRepository = $bankRepository;
    }

    public function getbanklist(){
        return $this->bankRepository->getallaccounts();
    }
    public function render()
    {
        return view('livewire.bankdetails',['banks'=>$this->getbanklist()]);
    }
}
