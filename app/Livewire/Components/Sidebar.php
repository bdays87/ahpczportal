<?php

namespace App\Livewire\Components;

use App\Interfaces\isystemmoduleInterface;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Sidebar extends Component
{

    protected $repository;
    public function boot(isystemmoduleInterface $repository)
    {
        $this->repository = $repository;
    }
    public function mount(){
        if(!Auth::check()){
            return redirect()->route('login');
        }
    }
    public function getuserpermissions(){
        return  Auth::user()->getPermissionsViaRoles()->pluck('name')->toArray();
     
        }
    public function render()
    {
        return view('livewire.components.sidebar',[
            'menulist' => $this->repository->getmenu(),
            'permissions' => $this->getuserpermissions()
        ]);
    }
}
