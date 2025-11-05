<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Mary\Traits\Toast;
use App\Interfaces\irenewalInterface;
use App\Interfaces\icurrencyInterface;
use App\Interfaces\iregistertypeInterface;
use App\Interfaces\idocumentInterface;
use App\Interfaces\itireInterface;
use App\Interfaces\iqualificationcategoryInterface;
class Renewalconfigurations extends Component
{
    use Toast;
    protected $renewalRepository;
    protected $currencyRepository;
    protected $registertypeRepository;
    protected $documentRepository;
    protected $tireRepository;
    protected $qualificationcategoryRepository;
    public $tab = 'renewalfees';
    public $breadcrumbs = [];
    public $renewalfees;

    public function mount()
    {
        $this->breadcrumbs =  [
            [
            'label' => 'Dashboard',
            'icon' => 'o-home',
            'link' => route('dashboard'),
        ],
        [
            'label' => 'Renewal Configurations'
        ],
    ];
    }
   
    public function render()
    {
        return view('livewire.admin.renewalconfigurations');
    }
}
