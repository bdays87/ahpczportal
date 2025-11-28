<?php

namespace App\Livewire\Components;

use Livewire\Component;
use App\Interfaces\iqualificationInterface;
use Illuminate\Support\Collection;
use Mary\Traits\Toast;
use App\Interfaces\institutionInterface;
class Qualifications extends Component
{
    use Toast;
    public $profession_id;
    public $name;
    public $search;
    public $qualifications;
    public $institution_id;
    public $modal = false;
    public $modifymodal = false;
    public $id;
    protected $repo;
    protected $institutionrepo;
    public function boot(iqualificationInterface $repo,institutionInterface $institutionrepo){
        $this->repo = $repo;
        $this->institutionrepo = $institutionrepo;
    }
    public function mount($profession_id){
        $this->profession_id = $profession_id;
        $this->qualifications = new Collection();
    }
    public function getinstitutions(){
        return $this->institutionrepo->getAllOptions();
    }
    public function getqualifications(){
         $this->qualifications = $this->repo->getAllQualifications($this->search,$this->profession_id);
    }

    public function showmodal(){
        $this->getqualifications();
        $this->modal = true;
    }
    public function newqualification(){
        $this->id = null;
        $this->name = null;
        $this->modifymodal = true;
    }
    public function showmodifymodal($id){
        $this->id = $id;
        $this->modifymodal = true;
    }
    public function headers():array{
        return [
            ['key' => 'name', 'label' => 'Name'],
            ['key' => 'institution.name', 'label' => 'Institution'],
            ['key' => 'action', 'label' => ''],
        ];
    }
    public function edit($id){
        $this->id = $id;
        $qualification = $this->repo->getQualificationById($id);
        $this->name = $qualification->name;
        $this->institution_id = $qualification->institution_id;
        $this->institution_id = $qualification->institution_id;
        $this->modifymodal = true;
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
     
        $response = $this->repo->createQualification(['name'=>$this->name,'profession_id'=>$this->profession_id,'institution_id'=>$this->institution_id]);
        if($response['status']=='success'){
            $this->success($response['message']);
            $this->getqualifications();
        }else{
            $this->error($response['message']);
        }
    }
    public function update(){
        
        $response = $this->repo->updateQualification(['name'=>$this->name,'profession_id'=>$this->profession_id,'institution_id'=>$this->institution_id],$this->id);
        if($response['status']=='success'){
            $this->success($response['message']);
            $this->getqualifications();
        }else{
            $this->error($response['message']);
        }
    }
    public function delete($id){
        $response = $this->repo->deleteQualification($id);
        if($response['status']=='success'){
            $this->success($response['message']);
            $this->getqualifications();
        }else{
            $this->error($response['message']);
        }
    }
    public function render()
    {
        return view('livewire.components.qualifications',[
            'headers' => $this->headers(),
            'institutions' => $this->getinstitutions(),
        ]);
    }
}
