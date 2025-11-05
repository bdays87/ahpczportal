<?php

namespace App\Livewire\Admin;

use App\Interfaces\icustomerprofessionInterface;
use Livewire\Component;
use Mary\Traits\Toast;

class Applicationapprovals extends Component
{
    use Toast;
    public $breadcrumbs = []; 
    protected $repo;
    public $status;
    public $year;
    public $customerprofession;
    public bool $viewmodal = false;
    public $search;
    public $fromdate;
    public $todate;
    public function mount()
    {
        $this->breadcrumbs = [
            [
                'label' => 'Dashboard',
                'icon' => 'o-home',
                'link' => route('dashboard'),
            ],
            [
                'label' => 'Applications'
            ],
        ];
        $this->customerprofession = null;
        $this->status = "AWAITING_APP";
        $this->year = date("Y");
    }

    public function boot(icustomerprofessionInterface $repo)
    {
        $this->repo = $repo;
    }

    public function getcustomerprofessions(){
        $data= $this->repo->getAll($this->status,$this->year);
   
        if($this->search){
            $data = $data->map(function($item){
                $item->customer->name = strtolower($item->customer->name);
                $item->customer->surname = strtolower($item->customer->surname);
                return $item;
            })->filter(function($item){
                return str_contains(strtolower($item->customer->name),strtolower($this->search)) || str_contains(strtolower($item->customer->surname),strtolower($this->search));
            });
        }
        if($this->fromdate){
            $data = $data->where('updated_at','>=',$this->fromdate);
        }
        if($this->todate){
            $data = $data->where('updated_at','<=',$this->todate);
        }
      
        return $data;
    }
    public function getcustomerprofession($id){
      $this->customerprofession = $this->repo->get($id);
      $this->viewmodal = true;

    }
    
    public function render()
    { 
        return view('livewire.admin.applicationapprovals',[
            'customerprofessions'=>$this->getcustomerprofessions(),
            
        ]);
    }
}
