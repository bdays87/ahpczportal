<?php

namespace App\Livewire\Admin;

use App\Interfaces\iaccounttypeInterface;
use App\Interfaces\iroleInterface;
use Livewire\Component;
use Mary\Traits\Toast;

class Roles extends Component
{
    use Toast;
    public $name;
    public $accounttype_id;
    public $id;
    public $modal = false;
    protected $repo;
    protected $accounttyperepo;
    public $breadcrumbs = [];
    public function mount()
    {
        $this->breadcrumbs = [
            [
                'label' => 'Dashboard',
                'icon' => 'o-home',
                'link' => route('dashboard'),
            ],
            [
                'label' => 'Roles'
            ],
        ];
    }
    public function boot(iroleInterface $repo,iaccounttypeInterface $accounttyperepo)
    {
        $this->repo = $repo;
        $this->accounttyperepo = $accounttyperepo;
    }
    public function getroles()
    {
        return $this->repo->getAll();
    }
    public function getaccounttypes()
    {
        return $this->accounttyperepo->getAll();
    }
    public function save()
    {
        $this->validate([
            'name' => 'required'
        ]);
        if($this->id){
            $this->update();
        }else{
            $this->create();
        }
        
        $this->reset(['name','accounttype_id','id']);
    }
    public function create(){
        $response = $this->repo->create([
            'name' => $this->name,
            'accounttype_id' => $this->accounttype_id
           
        ]);
        if ($response["status"] == "success") {
            $this->success($response["message"]);
        } else{
            $this->error($response["message"]);
        }
        
       
    }
    public function update()
    {
     
        $response = $this->repo->update($this->id,[
            'name' => $this->name,
            'accounttype_id' => $this->accounttype_id
        ]);
        if ($response["status"] == "success") {
            $this->success($response["message"]);
        } else{
            $this->error($response["message"]);
        }
       
    }
    public function delete($id)
    {
        $response = $this->repo->delete($id);
        if ($response['status'] == "success") {
            $this->success($response['message']);
        } else{
            $this->error($response['message']);
        }
    }
    public function edit($id)
    {
        $role = $this->repo->get($id);
        
        if (!$role) {
            $this->error('Role not found.');
            return;
        }
        $this->name = $role->name;
        $this->accounttype_id = $role->accounttype_id;
        $this->id = $id;
        $this->modal = true;
    }
    public function headers():array{
        return [
            ['key' => 'name', 'label' => 'Name'],
            ['key' => 'accounttype.name', 'label' => 'Account Type']
        ];
    }
  
    public function render()
    {
        return view('livewire.admin.roles',[
            'roles' => $this->getroles(),
            'headers' => $this->headers(),
            'accounttypes' => $this->getaccounttypes()
        ]);
    }
}
