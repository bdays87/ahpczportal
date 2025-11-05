<?php

namespace App\Livewire\Admin\Components;

use App\Interfaces\icustomerInterface;
use Illuminate\Support\Collection;
use Livewire\Component;

class Searchcustomer extends Component
{
    public $search;
    public $customers;
    public bool $modal = false;
    public function boot(icustomerInterface $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }
    public function mount()
    {
        $this->customers = new Collection();
    }

    public function getcustomers()
    {
        if($this->search){
            $this->modal = true;
            $this->customers = $this->customerRepository->getallsearch($this->search);
        }
        return [];
    }
    public function headers():array{
        return [
            ['key'=>'regnumber','label'=>'Regnumber'],
            ['key'=>'name','label'=>'Name'],
            ['key'=>'surname','label'=>'Surname'],
            ['key'=>'identificationnumber','label'=>'National ID'],
            ['key'=>'dob','label'=>'Date of Birth'],
            ['key'=>'gender','label'=>'Gender'],
            ['key'=>'nationality.name','label'=>'Nationality']
        ];
    }
    public function render()
    {
        return view('livewire.admin.components.searchcustomer',['customers'=>$this->getcustomers(),'headers'=>$this->headers()]);
    }
}
