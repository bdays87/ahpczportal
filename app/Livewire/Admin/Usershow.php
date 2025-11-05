<?php

namespace App\Livewire\Admin;

use App\Interfaces\iroleInterface;
use App\Interfaces\isystemmoduleInterface;
use App\Interfaces\iuserInterface;  
use Illuminate\Support\Str;
use Livewire\Component;
use Mary\Traits\Toast;

class Usershow extends Component
{
    use Toast;
    public $uuid;
    public $selectedTab = 'users-tab';
    public $user=null;
    public $search='';
    protected $userrepo;
    protected $rolerepo;
    protected $systemmodulerepo;
    public $assignedpermissions=[];
    public $assignedroles=[];
    public $breadcrumbs=[];
    public function boot(iuserInterface $userrepo,iroleInterface $rolerepo,isystemmoduleInterface $systemmodulerepo){
        $this->userrepo = $userrepo;
        $this->rolerepo = $rolerepo;
        $this->systemmodulerepo = $systemmodulerepo;
    }
    public function mount($uuid){
        $this->breadcrumbs = [
            [
                'label' => 'Dashboard',
                'icon' => 'o-home',
                'link' => route('dashboard'),
            ],
            [
                'label' => 'Users',
                'icon' => 'o-home',
                'link' => route('users.index'),
            ],
            [
                'label' => 'User profile'
            ]        ];
            
        $this->uuid = $uuid;
        $this->getuser();
        $this->getpermissions();
    }

    public function getuser(){
        $this->user = $this->userrepo->get_by_uuid($this->uuid);
    }

    public function getroles(){
       return $this->rolerepo->getbyaccounttype($this->user->accounttype_id);
    }

    public function getfullmenu(){
        if($this->search){
            return $this->systemmodulerepo->getfullmenubyaccounttype($this->user->accounttype_id)->filter(function ($menu) {
                return Str::contains($menu->name, $this->search)||$menu->submodules->filter(function ($submodule) {
                    return Str::contains($submodule->name, $this->search);
                })->isNotEmpty();
            });
        }else{
            return $this->systemmodulerepo->getfullmenubyaccounttype($this->user->accounttype_id);
        }
    }
    public function getpermissions(){
        $user =$this->userrepo->getRoles($this->user->id);
        $userroles = $user['roles'];
        $permissions = $user['permissions'];
        $this->assignedpermissions = array_merge($this->assignedpermissions,$permissions);
            $this->assignedroles = array_merge($this->assignedroles,$userroles);
      
    }
    public function assignrole($roleid){
       $response = $this->userrepo->assignrole($this->user->id,$roleid);
       if($response['status'] == 'success'){
           $this->getpermissions();
           $this->success($response['message']);
       }else{
           $this->error($response['message']);
       }
    }
    public function removerole($roleid){
       $response = $this->userrepo->removerole($this->user->id,$roleid);
       if($response['status'] == 'success'){
           $this->getpermissions();
           $this->success($response['message']);
       }else{
           $this->error($response['message']);
       }
    }
    public function assignpermission($permissionid){
       $response = $this->userrepo->assignpermission($this->user->id,$permissionid);
       if($response['status'] == 'success'){
           $this->getpermissions();
           $this->success($response['message']);
       }else{
           $this->error($response['message']);
       }
    }
    public function removepermission($permissionid){
       $response = $this->userrepo->removepermission($this->user->id,$permissionid);
       if($response['status'] == 'success'){
           $this->getpermissions();
           $this->success($response['message']);
       }else{
           $this->error($response['message']);
       }
    }
    public function render()
    {
        return view('livewire.admin.usershow',[
            'user'=>$this->user,
            'roles'=>$this->getroles(),
            'menulist'=>$this->getfullmenu(),
            'permissions'=>$this->assignedpermissions,
        ]);
    }
}
