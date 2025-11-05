<?php

namespace App\Livewire\Admin\Components;

use App\Interfaces\ipermissionInterface;
use Livewire\Component;
use Mary\Traits\Toast;

class Permissions extends Component
{
   use Toast;
    public $modal = false;
    public $name;
    public $submodule_id;
    public $id;
    public $permissions;
    protected $repo;
    public function boot(ipermissionInterface $repo){
        $this->repo = $repo;
    }
    public function mount($submodule_id){
        $this->submodule_id = $submodule_id;
    }
        
   
    public function getPermissions(){
        $this->permissions = $this->repo->getPermissions($this->submodule_id);
        $this->modal = true;
    }

    public function save(){
        $this->validate([
            'name' => 'required',
        ]);
        if($this->id){
            $this->update();
        }else{
            $this->create();
        }
        $this->reset(['name','id']);
    }

    public function update(){
        $response = $this->repo->update($this->id, [
            'name' => $this->name,
            'submodule_id' => $this->submodule_id,
        ]);
        if ($response["status"] == "success") {
            $this->success($response["message"]);
            $this->getPermissions();
        } else{
            $this->error($response["message"]);
        }
    }

    public function create(){
        $response = $this->repo->create([
            'name' => $this->name,
            'submodule_id' => $this->submodule_id,
        ]);
        if ($response["status"] == "success") {
            $this->success($response["message"]);
            $this->getPermissions();
        } else{
            $this->error($response["message"]);
        }
    }   
    public function delete($id){
        $response = $this->repo->delete($id);
        if ($response['status'] == "success") {
            $this->success($response['message']);
            $this->getPermissions();
        } else{
            $this->error($response['message']);
        }
    }
    public function edit($id){
        $permission = $this->repo->get($id);
        if (!$permission) {
            $this->error('Permission not found.');
            return;
        }
        $this->name = $permission->name;
        $this->id = $id;
    }

    public function render()
    {
        return view('livewire.admin.components.permissions');
    }
}
