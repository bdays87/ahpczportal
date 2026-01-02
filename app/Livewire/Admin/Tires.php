<?php

namespace App\Livewire\Admin;

use App\Interfaces\icustomertypeInterface;
use App\Interfaces\idocumentInterface;
use App\Interfaces\itireInterface;
use App\Interfaces\iapplicationtypeInterface;
use Illuminate\Support\Collection;
use Livewire\Component;
use Mary\Traits\Toast;

class Tires extends Component
{
    use Toast;
    public $name;
    public $modal = false;
    public $modifymodal = false;
    public $assignedDocuments = [];
    public $documentmodal = false;
    public $document_id;
    public $customertype_id;
    public $applicationtype_id;
    public $search;
    public $id;
    protected $repo;
    protected $documentrepo;
    protected $customertyperepo;
    protected $applicationtyperepo;
    public $breadcrumbs=[];
    public function boot(itireInterface $repo, idocumentInterface $documentrepo, icustomertypeInterface $customertyperepo, iapplicationtypeInterface $applicationtyperepo)
    {
        $this->repo = $repo;
        $this->documentrepo = $documentrepo;
        $this->customertyperepo = $customertyperepo;
        $this->applicationtyperepo = $applicationtyperepo;
    }
    public function mount()
    {
        $this->breadcrumbs = [
            [
                'label' => 'Dashboard',
                'icon' => 'o-home',
                'link' => route('dashboard'),
            ],
            [
                'label' => 'Tires'
            ],
        ];
        $this->assignedDocuments = new Collection();
    }
    public function gettires()
    {
        return $this->repo->getAll($this->search);
    }
    public function getdocuments()
    {
        return $this->documentrepo->getAll($this->search);
    }
    public function getapplicationtypes()
    {
        return $this->applicationtyperepo->getAll();
    }
    public function getcustomertypes()
    {
        return $this->customertyperepo->getAll();
    }
    public function save(){

        $this->validate([
            'name' => 'required|string|max:255'
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
            'applicationtype_id' => $this->applicationtype_id,
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
        $tire = $this->repo->get($id);
        $this->id = $tire->id;
        $this->name = $tire->name;
        $this->modal = true;
    }
    public function headers(){
        return [
            ['key'=>'name','label'=>'Name','sortable'=>true],            
            ['key'=>'action','label'=>'','sortable'=>false]
        ];
    }
    public function getassigneddocuments($id){
        $this->id = $id;
        $this->assignedDocuments = $this->repo->getDocuments($id);
        $this->documentmodal = true;
    }
    public function assignDocument(){
        $this->validate([
            'document_id' => 'required',
            'customertype_id' => 'required',
            'applicationtype_id' => 'required',
        ]);
        $response = $this->repo->assigndocument($this->id, $this->document_id,$this->customertype_id,$this->applicationtype_id);
        if($response['status']=='success'){
            $this->success($response['message']);
            $this->assignedDocuments = $this->repo->getDocuments($this->id);
        }else{
            $this->error($response['message']);
        }
       
    }
    public function unassignDocument($id){
     
        $response = $this->repo->unassigndocument($id);
        if($response['status']=='success'){
            $this->success($response['message']);
            $this->assignedDocuments = $this->repo->getDocuments($this->id);
        }else{
            $this->error($response['message']);
        }
    }
        
    public function render()
    {
        return view('livewire.admin.tires',[
            'tires' => $this->gettires(),
            'headers' => $this->headers(),
            'documents' => $this->getdocuments(),
            'customertypes' => $this->getcustomertypes(),
            'applicationtypes' => $this->getapplicationtypes(),
        ]);
    }
}
