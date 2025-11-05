<?php

namespace App\Livewire\Admin\Components;

use App\Interfaces\iqualificationcategoryInterface;
use Livewire\Component;
use Mary\Traits\Toast;

class Qualificationcategories extends Component
{
    use Toast;
    protected $repo;
    public $modal = false;
    public $modifymodal = false;
    public $id;
    public $name;
    public $requireapproval;
    public $search;
    public function boot(iqualificationcategoryInterface $repo){
        $this->repo = $repo;
    }
    public function getqualificationcategories(){
        return $this->repo->getAll($this->search);
    }
    public function headers():array{
        return [
            ['key' => 'name', 'label' => 'Name'],
            ['key' => 'requireapproval', 'label' => 'Require approval'],
            ['key' => 'action', 'label' => ''],
        ];
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
         $response = $this->repo->create(['name'=>$this->name,'requireapproval'=>$this->requireapproval]);
         if($response['status']=='success'){
            $this->success($response['message']);
         }else{
            $this->error($response['message']);
         }
    }
    public function update(){
         $response = $this->repo->update($this->id,['name'=>$this->name,'requireapproval'=>$this->requireapproval]);
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
        $this->requireapproval = $this->repo->get($id)->requireapproval;
        $this->modifymodal = true;
    }
    public function render()
    {
        return view('livewire.admin.components.qualificationcategories',[
            'qualificationcategories'=>$this->getqualificationcategories(),
            'headers'=>$this->headers()
        ]);
    }
}
