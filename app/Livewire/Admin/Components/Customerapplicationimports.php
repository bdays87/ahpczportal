<?php

namespace App\Livewire\Admin\Components;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Mary\Traits\Toast;
use App\Interfaces\idatamanagementInterface;

class Customerapplicationimports extends Component
{
    use Toast,WithFileUploads,WithPagination;
    public $file;
    public $search;
    public $id;
    public $importmodal = false;
    public $editmodal = false;
    public $regnumber;
    public $prefix;
    public $applicationtype;
    public $registertype;
    public $certificatenumber;
    public $registrationdate;
    public $certificateexpirydate;
    public $year;
    public $status;
    protected  $datamanagementrepo;
    public function boot(idatamanagementInterface $datamanagementrepo)
    {
        $this->datamanagementrepo = $datamanagementrepo;
    }
    public function getcustomerapplicationimports()
    {
        return $this->datamanagementrepo->getallcustomerapplicationimports($this->search);
    }
    public function headers(): array
    {
        return [
            ['key'=>'regnumber','label'=>'RegNumber','sortable'=>true],
            ['key'=>'prefix','label'=>'Prefix','sortable'=>true],
            ['key'=>'applicationtype','label'=>'ApplicationType','sortable'=>true],
            ['key'=>'registertype','label'=>'RegisterType','sortable'=>true],
            ['key'=>'certificatenumber','label'=>'CertificateNumber','sortable'=>true],
            ['key'=>'registrationdate','label'=>'RegistrationDate','sortable'=>true],
            ['key'=>'certificateexpirydate','label'=>'CertificateExpiryDate','sortable'=>true],
            ['key'=>'year','label'=>'Year','sortable'=>true],
            ['key'=>'status','label'=>'Status','sortable'=>true],
            ['key'=>'proceeded','label'=>'Proceeded','sortable'=>true],
            ['key'=>'action','label'=>'','sortable'=>false],
        ];
    }
    public function saveimport()
    {
        $this->validate([
            'file' => 'required|file|mimes:csv,txt',
        ]);
        $path = $this->file->store('customerapplicationimports');
        $response = $this->datamanagementrepo->importcustomerapplications($path);
        if ($response['status'] == 'success') {
            $this->success($response['message']);
        } else {
          dd($response['message']);
        }
    } 

    public function save(){
        $this->validate([
            'regnumber' => 'required|string|max:255',
            'prefix' => 'required|string|max:255',
            'applicationtype' => 'required|string|max:255',
            'registertype' => 'required|string|max:255',
            'certificatenumber' => 'required|string|max:255',
            'registrationdate' => 'required|string|max:255',
            'certificateexpirydate' => 'required|string|max:255',
            'year' => 'required|integer',
        ]);
        if($this->id){
            $this->update();
        }else{
            $this->create();
        }   
        $this->reset('regnumber','prefix','applicationtype','registertype','certificatenumber','registrationdate','certificateexpirydate','year','status','id','editmodal');
    }

    public function create(){
        $response = $this->datamanagementrepo->createcustomerapplicationimport([
            'regnumber' => $this->regnumber,
            'prefix' => $this->prefix,
            'applicationtype' => $this->applicationtype,
            'registertype' => $this->registertype,
            'certificatenumber' => $this->certificatenumber,
            'registrationdate' => $this->registrationdate,
            'certificateexpirydate' => $this->certificateexpirydate,
            'year' => $this->year,
            'status' => $this->status,
        ]);
        if ($response['status'] == 'success') {
            $this->success($response['message']);
        } else {
            $this->error($response['message']);
        }
    }
    public function update(){
        $response = $this->datamanagementrepo->updatecustomerapplicationimport($this->id,[
            'regnumber' => $this->regnumber,
            'prefix' => $this->prefix,
            'applicationtype' => $this->applicationtype,
            'registertype' => $this->registertype,
            'certificatenumber' => $this->certificatenumber,
            'registrationdate' => $this->registrationdate,
            'certificateexpirydate' => $this->certificateexpirydate,
            'year' => $this->year,
            'status' => $this->status,
        ]);
        if ($response['status'] == 'success') {
            $this->success($response['message']);
        } else {
            $this->error($response['message']);
        }
    }
    public function editcustomerapplication($id)
    {
        $this->id = $id;
        $customerapplicationimport = $this->datamanagementrepo->getcustomerapplicationimport($id);
        $this->regnumber = $customerapplicationimport->regnumber;
        $this->prefix = $customerapplicationimport->prefix;
        $this->applicationtype = $customerapplicationimport->applicationtype;
        $this->registertype = $customerapplicationimport->registertype;
        $this->certificatenumber = $customerapplicationimport->certificatenumber;
        $this->registrationdate = $customerapplicationimport->registrationdate;
        $this->certificateexpirydate = $customerapplicationimport->certificateexpirydate;
        $this->year = $customerapplicationimport->year;
        $this->status = $customerapplicationimport->status;
        $this->editmodal = true;
     
    }
    public function deletecustomerapplication($id)
    {
        $response = $this->datamanagementrepo->deletecustomerapplicationimport($id);
        if ($response['status'] == 'success') {
            $this->success($response['message']);
        } else {
            $this->error($response['message']);
        }
    }
    public function render()
    {
        return view('livewire.admin.components.customerapplicationimports', ['customerapplicationimports' => $this->getcustomerapplicationimports(), 'headers' => $this->headers()]);
    }
}
