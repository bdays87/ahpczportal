<?php

namespace App\Livewire\Admin\Components;

use App\Interfaces\inationalityInterface;
use Livewire\Component;
use Mary\Traits\Toast;

class Nationalities extends Component
{
    use Toast;
    public $name;
    public $search;
    public $modifymodal = false;
    public $id;
    protected $repo;
    public $modal = false;
    public function boot(inationalityInterface $repo)
    {
        $this->repo = $repo;
    }
    public function getnationalities()
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
        $nationality = $this->repo->get($id);
        $this->id = $nationality->id;
        $this->name = $nationality->name;
        $this->modifymodal = true;
    }
    public function headers(){
        return [['key'=>'name','label'=>'Name','sortable'=>true],['key'=>'action','label'=>'','sortable'=>false]];
    }
    public function render()
    {
        return view('livewire.admin.components.nationalities',[
            'nationalities' => $this->getnationalities(),
            'headers' => $this->headers(),
        ]);
    }
}
