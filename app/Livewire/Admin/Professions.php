<?php

namespace App\Livewire\Admin;

use App\Interfaces\icustomertypeInterface;
use App\Interfaces\idocumentInterface;
use App\Interfaces\iprofessionInterface;
use App\Interfaces\iregistertypeInterface;
use App\Interfaces\itireInterface;
use Illuminate\Support\Collection;
use Livewire\Component;
use Mary\Traits\Toast;

class Professions extends Component
{
    use Toast;
    public $search;
    public $tire_id;
    public $filtertire_id;
    public $name;
    public $status;
    public $id;
    public $requiredcdp;
    public $minimumcpd;
    public $breadcrumbs=[];
    public $modal = false;
    public $conditionmodal = false;
    public $documentmodal = false;
    public $document_id;
    public $customertype_id;
    public $condition;
    public $condition_id;
    public $prefix;
    public $assignedDocuments;
    public $profession=null;
    protected $tirerepo;
    protected $professionrepo;
    protected $documentrepo;
    protected $customertype_repo;

    public function mount(){
        $this->breadcrumbs = [
            [
                'label' => 'Dashboard',
                'icon' => 'o-home',
                'link' => route('dashboard'),
            ],
            [
                'label' => 'Professions'
            ],
        ];
        $this->assignedDocuments= new Collection();
    }
        
    public function boot(itireInterface $tirerepo, iprofessionInterface $professionrepo, idocumentInterface $documentrepo,icustomertypeInterface $customertype_repo)
    {
        $this->tirerepo = $tirerepo;
        $this->professionrepo = $professionrepo;
        $this->documentrepo = $documentrepo;
        $this->customertype_repo = $customertype_repo;
    }

    public function getTires()
    {
        return $this->tirerepo->getAll();
    }
    public function getCustomertypes()
    {
        return $this->customertype_repo->getAll();
    }
    public function getDocuments()
    {
        return $this->documentrepo->getAll($this->search);
    }
    public function getProfessions()
    {
        return $this->professionrepo->getAll($this->search,$this->filtertire_id);
    }
  
    public function save(){
        $this->validate([
            'name' => 'required',
            'status' => 'required',
            'requiredcdp' => 'required',
            'minimumcpd' => 'required',
            'tire_id' => 'required',
            'prefix' => 'required',
        ]);

        if($this->id){

            $this->update();
        }else{
            $this->create();
        }

    }


    public function create(){

        $response = $this->professionrepo->create([
            'name' => $this->name,
            'status' => $this->status,
            'required_cdp' => $this->requiredcdp,
            'minimum_cdp' => $this->minimumcpd,
            'tire_id' => $this->tire_id,
            'prefix' => $this->prefix,
        ]);

        if($response['status']=="success"){
            $this->success($response['message']);
          
        }else{
            $this->error($response['message']);
        }
    }


    public function update(){

        $response = $this->professionrepo->update($this->id, [
            'name' => $this->name,
            'status' => $this->status,
            'required_cdp' => $this->requiredcdp,
            'minimum_cdp' => $this->minimumcpd,
            'tire_id' => $this->tire_id,
            'prefix' => $this->prefix,
        ]);

        if($response['status']=="success"){
            $this->success($response['message']);
          
        }else{
            $this->error($response['message']);
        }
    }

    public function delete(){
        $response = $this->professionrepo->delete($this->id);
        if($response['status']=="success"){
            $this->success($response['message']);
          
        }else{
            $this->error($response['message']);
        }
    }

    public function edit($id){
        $this->id = $id;
        $profession = $this->professionrepo->get($id);
        $this->name = $profession->name;
        $this->status = $profession->status;
        $this->requiredcdp = $profession->required_cdp;
        $this->minimumcpd = $profession->minimum_cdp;
        $this->tire_id = $profession->tire_id;
        $this->prefix = $profession->prefix;
        $this->modal = true;
    }

    public function  getassignedDocuments($id){
        $this->id = $id;
        $this->assignedDocuments = $this->professionrepo->getDocuments($id);
        $this->documentmodal = true;
    }
    public function assignDocument(){
        $this->validate([
            'document_id' => 'required',
            'customertype_id' => 'required',
        ]);
        $response = $this->professionrepo->assigndocument($this->id, $this->document_id,$this->customertype_id);
        if($response['status']=="success"){
            $this->success($response['message']);
            $this->assignedDocuments = $this->professionrepo->getDocuments($this->id);
          
        }else{
            $this->error($response['message']);
        }
    }
    public function unassignDocument($id){
        $response = $this->professionrepo->unassigndocument($id);
        if($response['status']=="success"){
            $this->success($response['message']);
            $this->assignedDocuments = $this->professionrepo->getDocuments($this->id);          
        }else{
            $this->error($response['message']);
        }
    }

    public function openconditionmodal($id){
        $this->id = $id;
        $this->profession = $this->professionrepo->get($id);
        $this->conditionmodal = true;
    }

    public function savecondition(){
        $this->validate([
            'customertype_id' => 'required',
            'condition' => 'required',
        ]);
        if($this->condition_id){
            $this->updatecondition();
        }else{
            $this->createconditon();
        }

        $this->reset("customertype_id", "condition");

    }

    public function editcondition($id){
        $this->condition_id = $id;
        $condition = $this->professionrepo->getcondition($id);
        $this->customertype_id = $condition->customertype_id;
        $this->condition = $condition->condition;
     
    }

    public function createconditon(){
        $response = $this->professionrepo->createcondition([
            'profession_id'=>$this->id,
            'customertype_id'=>$this->customertype_id,
            'condition'=>$this->condition
        ]);
        if($response['status']=="success"){
            $this->success($response['message']);
          
        }else{
            $this->error($response['message']);
        }
    }

    public function updatecondition(){
        $response = $this->professionrepo->updatecondition($this->condition_id, [
            'customertype_id'=>$this->customertype_id,
            'condition'=>$this->condition
        ]);
        if($response['status']=="success"){
            $this->success($response['message']);
          
        }else{
            $this->error($response['message']);
        }
    }

    public function deletecondition(){
        $response = $this->professionrepo->deletecondition($this->condition_id);
        if($response['status']=="success"){
            $this->success($response['message']);
          
        }else{
            $this->error($response['message']);
        }
    }

    public function headers(){
        return [
            ['key' => 'name', 'label' => 'Name'],
            ['key' => 'status', 'label' => 'Status'],
            ['key' => 'required_cdp', 'label' => 'Required CDP'],
            ['key' => 'minimum_cdp', 'label' => 'Minimum CDP'],
            ['key' => 'tire.name', 'label' => 'Tire'],
            ['key' => 'actions', 'label' => ''],
        ];
    }
    public function render()
    {
        return view('livewire.admin.professions', [
            'headers' => $this->headers(),
            'professions' => $this->getProfessions(),
            'documents' => $this->getDocuments(),
            'customertypes' => $this->getCustomertypes(),
            'tires' => $this->getTires()
        ]);
    }
}
