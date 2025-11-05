<?php

namespace App\Livewire\Admin;

use App\Interfaces\iroleInterface;
use App\Interfaces\isystemmoduleInterface;
use Illuminate\Support\Str;
use Livewire\Component;
use Mary\Traits\Toast;

class Roleshow extends Component
{
    use Toast;
    public $role_id;
    protected $repo;
    protected $systemmodulerepo;
    public $breadcrumbs=[];
    public $role = null;
    public $permissions = [];
    public $search = '';
    public function boot(iroleInterface $repo, isystemmoduleInterface $systemmodulerepo){
        $this->repo = $repo;
        $this->systemmodulerepo = $systemmodulerepo;
    }
    public function mount($role_id){
        $this->role_id = $role_id;
        $this->breadcrumbs = [
            [
                'label' => 'Dashboard',
                'icon' => 'o-home',
                'link' => route('dashboard'),
            ],
            [
                'label' => 'Roles',
                'icon' => 'o-lock-closed',
                'link' => route('roles.index'),
            ],
            [
                'label' => 'Role Show'
            ],
        ];
    }

    public function getfullmenu(){
    if($this->search){
        return $this->systemmodulerepo->getmenu()->filter(function ($menu) {
            return Str::contains($menu->name, $this->search)||$menu->submodules->filter(function ($submodule) {
                return Str::contains($submodule->name, $this->search);
            })->isNotEmpty();
        });
    }else{
        return $this->systemmodulerepo->getmenu();
    }
    }
    public function assignedpermissions(){
        $this->role =$this->repo->getPermissions($this->role_id);
        return $this->role->permissions->pluck('id')->toArray();
    }
    public function assignpermission($permission_id){
        $response = $this->repo->assignpermission($this->role_id, $permission_id);
        if($response['status'] == 'success'){
            $this->success($response['message']);
        }else{
            $this->error($response['message']);
        }
    }
    public function removepermission($permission_id){
       $response = $this->repo->removepermission($this->role_id, $permission_id);
       if($response['status'] == 'success'){
           $this->success($response['message']);
       }else{
           $this->error($response['message']);
       }
    }
    public function render()
    {
        return view('livewire.admin.roleshow', [
            'fullmenu' => $this->getfullmenu(),
            'assignedpermissions' => $this->assignedpermissions()
        ]);
    }
}
