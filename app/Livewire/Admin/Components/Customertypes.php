<?php

namespace App\Livewire\Admin\Components;

use App\Interfaces\icustomertypeInterface;
use App\Interfaces\iregistertypeInterface;
use Livewire\Component;
use Mary\Traits\Toast;

class Customertypes extends Component
{
    use Toast;
    public $name;
    public $id;
    protected $repo;
    protected $registertyperepo;
    public $modal = false;
    public $modifymodal = false;
    public $assignmodal = false;
    public $registertype_id;
    public function boot(icustomertypeInterface $repo,iregistertypeInterface $registertyperepo){
        $this->repo = $repo;
        $this->registertyperepo = $registertyperepo;
    }
    

    public function getcustomertypes(){
        return $this->repo->getAll();
    }
    public function getregistertypes(){
        return $this->registertyperepo->getAll();
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
            ['key' => 'registertypes', 'label' => 'Register types'],
            ['key' => 'action', 'label' => ''],
        ];
    }

    public function showassignmodal($id){
        $this->id = $id;
        $this->assignmodal = true;
    }
    public function assign(){
        $response = $this->repo->assignregistertype($this->id,$this->registertype_id);
        if($response['status']=='success'){
            $this->success($response['message']);
        }else{
            $this->error($response['message']);
        }
    }
    public function removeregistertype($id,$registertype_id){
        $response = $this->repo->removeregistertype($id,$registertype_id);
        if($response['status']=='success'){
            $this->success($response['message']);
        }else{
            $this->error($response['message']);
        }
    }
    public function render()
    {
        return view('livewire.admin.components.customertypes', [
            'customertypes' => $this->getcustomertypes(),
            'headers' => $this->headers(),
            'registertypes'=>$this->getregistertypes()
        ]);
    }
}
