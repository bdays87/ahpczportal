<?php

namespace App\Livewire\Admin\Components;

use App\Interfaces\icityInterface;
use App\Interfaces\iprovinceInterface;
use Livewire\Component;
use Mary\Traits\Toast;

class Cities extends Component
{
    use Toast;
    protected $repo;
    protected $provincerepo;
    public $name;
    public $search;
    public $modal = false;
    public $modifymodal = false;
    public $id;
    public $province_id;
    public function boot(icityInterface $repo, iprovinceInterface $provincerepo)
    {
        $this->repo = $repo;
        $this->provincerepo = $provincerepo;
    }
    public function getprovinces()
    {
        return $this->provincerepo->getAll();
    }
    public function getcities()
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
            'province_id' => $this->province_id,
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
            'province_id' => $this->province_id,
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
        $city = $this->repo->get($id);
        $this->id = $city->id;
        $this->name = $city->name;
        $this->province_id = $city->province_id;
        $this->modifymodal = true;
    }
    public function headers(){
        return [
            ['key'=>'name','label'=>'Name','sortable'=>true],
            ['key'=>'province.name','label'=>'Province','sortable'=>true],
            ['key'=>'action','label'=>'','sortable'=>false]
        ];
    }
    public function render()
    {
        return view('livewire.admin.components.cities',[
            'cities' => $this->getcities(),
            'provinces' => $this->getprovinces(),
            'headers' => $this->headers(),
        ]);
    }
}
