<?php

namespace App\Livewire\Admin\Components;

use App\Interfaces\icertificatetypeInterface;
use Livewire\Component;
use Mary\Traits\Toast;

class Certificatetypes extends Component
{
    use Toast;
    public $name;
    public $id;
    protected $repo;
    public $modifymodal = false;
    public $modal = false;
    public function boot(icertificatetypeInterface $repo){
        $this->repo = $repo;
    }
    

    public function getcertificatetypes(){
        return $this->repo->getAll();
    }

    public function save(){
        $this->validate(['name'=>'required']);
        if($this->id){
            $this->update();
        }else{
            $this->create();
        }
        $this->reset(['name','id']);

    }

    public function create(){
         $response = $this->repo->create(['name'=>$this->name]);
         if($response['status']=='success'){
            $this->success($response['message']);
         }else{
            $this->error($response['message']);
         }
    }
    public function update(){
         $response = $this->repo->update($this->id,['name'=>$this->name]);
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
        $this->id = $id;
        $this->name = $this->repo->get($id)->name;
        $this->modifymodal = true;
    }

    public function headers():array{
        return [
            ['key' => 'name', 'label' => 'Name'],
            ['key' => 'action', 'label' => ''],
        ];
    }
    public function render()
    {
        return view('livewire.admin.components.certificatetypes',[
            'certificatetypes'=>$this->getcertificatetypes(),
            'headers'=>$this->headers(),
        ]);
    }
}
