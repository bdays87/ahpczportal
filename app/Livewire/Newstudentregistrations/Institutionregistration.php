<?php

namespace App\Livewire\Newstudentregistrations;

use Livewire\Component;
use App\Interfaces\icustomerprofessionInterface;
use App\Interfaces\istudentInterface;
use App\Interfaces\iqualificationlevelInterface;
use App\Interfaces\icurrencyInterface;
use Illuminate\Support\Facades\Auth;

class Institutionregistration extends Component
{
    public $uuid;
    public $breadcrumbs=[];
    public $customerprofession_id;
    protected $repo;
    protected $studentrepo;
    protected $qualificationlevelrepo;
    protected $currencyrepo;
    public function mount($uuid){
        $this->uuid = $uuid;
        if(Auth::user()->accounttype_id == 1){
        $this->breadcrumbs = [
            [
                'label' => 'Dashboard',
                'icon' => 'o-home',
                'link' => route('dashboard'),
            ],
            [
                'label' => 'Customer',
                'icon' => 'o-home',
                'link' => route('customers.index'),
            ],
            [
                'label' => 'Customer Student'
            ],
        ];
        }else{
            $this->breadcrumbs = [
                [
                    'label' => 'Dashboard',
                    'icon' => 'o-home',
                    'link' => route('dashboard'),
                ],
                [
                    'label' => 'My Profession'
                ],
            ];
        }
    }
    public function boot(icustomerprofessionInterface $repo,istudentInterface $studentrepo,iqualificationlevelInterface $qualificationlevelrepo,icurrencyInterface $currencyrepo){
        $this->repo = $repo;
        $this->studentrepo = $studentrepo;
        $this->qualificationlevelrepo = $qualificationlevelrepo;
        $this->currencyrepo = $currencyrepo;
    }
    public function getdata(){
        $data = $this->repo->getcustomerstudent($this->uuid);
  
        $this->customerprofession_id = $data['customerprofession']->id;
        return $data;
    }
    public function getcurrencies(){
        return $this->currencyrepo->getAll("active");
    }
    public function getqualificationlevels(){
        return $this->qualificationlevelrepo->getAll();
    }
    public function render()
    {
        return view('livewire.newstudentregistrations.institutionregistration');
    }
}
