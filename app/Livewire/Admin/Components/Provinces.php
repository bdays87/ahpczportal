<?php

namespace App\Livewire\Admin\Components;

use Livewire\Component;
use App\Interfaces\iprovinceInterface;
use Mary\Traits\Toast;

class Provinces extends Component
{
    use Toast;
    protected $repo;
    public $name;
    public $search;
    public $modal = false;
    public $modifymodal = false;
    public $id;
    public function boot(iprovinceInterface $repo)
    {
        $this->repo = $repo;
    }
    public function getprovinces()
    {
        return $this->repo->getAll($this->search);
    }
    public function save(){
        $this->validate([
            'name' => 'required|string|max:255',
        ]);
       if($this->id){
           $this->update();
       }else{
           $this->create();
       }
       $this->reset(['name','id']);
    }
    public function update(){
        $response =$this->repo->update($this->id, [
            'name' => $this->name,
        ]);
        if($response['status']=='success'){
            $this->success($response['message']);
        }else{
            $this->error($response['message']);
        }
       
    }
    public function create(){
        $response = $this->repo->create([
            'name' => $this->name,
        ]);
        if($response['status']=='success'){
            $this->success($response['message']);
        }else{
            $this->error($response['message']);
        }
    }
    public function delete($id){
        $response = $this->repo->delete($id);
        if($response['status']=='success'){
            $this->success($response['message']);
        }else{
            $this->error($response['message']);
        }
    }
    public function edit($id){
        $province = $this->repo->get($id);
        $this->id = $province->id;
        $this->name = $province->name;
        $this->modifymodal = true;
    }
    public function headers(){
        return [['key'=>'name','label'=>'Name','sortable'=>true],['key'=>'action','label'=>'','sortable'=>false]];
    }
    public function render()
    {
        return view('livewire.admin.components.provinces',[
            'provinces' => $this->getprovinces(),
            'headers' => $this->headers(),
        ]);
    }
}
