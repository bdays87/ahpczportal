<?php

namespace App\Livewire\Admin\Components;

use Livewire\Component;
use Mary\Traits\Toast;
use App\Interfaces\iapplicationtypeInterface;

class Applicanttypes extends Component
{
    use Toast;
    public $name;
    public $description;
    public $id;
    protected $repo;
    public $modifymodal = false;
    public $modal = false;
    public function boot(iapplicationtypeInterface $repo){
        $this->repo = $repo;
    }
    

    public function getapplicanttypes(){
        return $this->repo->getAll();
    }

    public function save(){
        $this->validate(['name'=>'required','description'=>'required']);
        if($this->id){
            $this->update();
        }else{
            $this->create();
        }
        $this->reset(['name','id','description']);

    }

    public function create(){
         $response = $this->repo->create(['name'=>$this->name,'description'=>$this->description]);
         if($response['status']=='success'){
            $this->success($response['message']);
         }else{
            $this->error($response['message']);
         }
    }
    public function update(){
         $response = $this->repo->update($this->id,['name'=>$this->name,'description'=>$this->description]);
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
        $this->description = $this->repo->get($id)->description;
        $this->modifymodal = true;
    }

    public function headers():array{
        return [
            ['key' => 'name', 'label' => 'Name'],
            ['key' => 'description', 'label' => 'Description'],
            ['key' => 'action', 'label' => ''],
        ];
    }
    public function render()
    {
        return view('livewire.admin.components.applicanttypes',[
            'applicanttypes'=>$this->getapplicanttypes(),
            'headers'=>$this->headers(),
        ]);
    }
}
