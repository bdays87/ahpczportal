<?php

namespace App\Livewire\Admin\Components;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Mary\Traits\Toast;
use App\Interfaces\idatamanagementInterface;

class Customerregistrationimports extends Component
{
    use Toast,WithFileUploads,WithPagination;
    public $file;
    public $search;
    public $id;
    public $importmodal = false;
    public $editmodal = false;
    public $regnumber;
    public $prefix;
    public $certificatenumber;
    public $registrationdate;
    public $status;
    protected  $datamanagementrepo;
    public function boot(idatamanagementInterface $datamanagementrepo)
    {
        $this->datamanagementrepo = $datamanagementrepo;
    }
    public function getcustomerregistrationimports()
    {
        return $this->datamanagementrepo->getallcustomerregistrationimports($this->search);
    }

    public function headers(): array
    {
        return [
            ['key'=>'regnumber','label'=>'RegNumber','sortable'=>true],
            ['key'=>'prefix','label'=>'Prefix','sortable'=>true],
            ['key'=>'certificatenumber','label'=>'CertificateNumber','sortable'=>true],
            ['key'=>'registrationdate','label'=>'RegistrationDate','sortable'=>true],
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
        $path = $this->file->store('customerregistrationimports');
        $response = $this->datamanagementrepo->importcustomerregistrations($path);
        if ($response['status'] == 'success') {
            $this->success($response['message']);
        } else {
            $this->error($response['message']);
        }
    }

    public function savecustomerregistration()
    {
        $this->validate([
            'regnumber' => 'required',
            'prefix' => 'required',
            'certificatenumber' => 'required',
            'registrationdate' => 'required',
            'status' => 'required',
        ]);
        if($this->id){
            $this->update();
        }else{
            $this->create();
        }
        $this->reset(['regnumber','prefix','certificatenumber','registrationdate','status','id','editmodal']);
    }
    public function update(){
        $this->validate([
            'regnumber' => 'required',
            'prefix' => 'required',
            'certificatenumber' => 'required',
            'registrationdate' => 'required',
            'status' => 'required',
        ]);
        $response = $this->datamanagementrepo->updatecustomerregistrationimport($this->id, [
            'regnumber' => $this->regnumber,
            'prefix' => $this->prefix,
            'certificatenumber' => $this->certificatenumber,
            'registrationdate' => $this->registrationdate,
            'status' => $this->status,
        ]);
        if ($response['status'] == 'success') {
            $this->success($response['message']);
        } else {
            $this->error($response['message']);
        }
    }
    public function create(){
        $response = $this->datamanagementrepo->createcustomerregistrationimport([
            'regnumber' => $this->regnumber,
            'prefix' => $this->prefix,
            'certificatenumber' => $this->certificatenumber,
            'registrationdate' => $this->registrationdate,
            'status' => $this->status,
        ]);
        if ($response['status'] == 'success') {
            $this->success($response['message']);
        } else {
            $this->error($response['message']);
        }
    }
    public function edit($id)
    {
        $this->id = $id;
        $customerregistrationimport = $this->datamanagementrepo->getcustomerregistrationimport($id);
        $this->regnumber = $customerregistrationimport->regnumber;
        $this->prefix = $customerregistrationimport->prefix;
        $this->certificatenumber = $customerregistrationimport->certificatenumber;
        $this->registrationdate = $customerregistrationimport->registrationdate;
        $this->status = $customerregistrationimport->status;
        $this->editmodal = true;
    }
    public function delete($id)
    {
        $response = $this->datamanagementrepo->deletecustomerregistrationimport($id);
        if ($response['status'] == 'success') {
            $this->success($response['message']);
        } else {
            $this->error($response['message']);
        }
    }
    public function render()
    {
        return view('livewire.admin.components.customerregistrationimports', ['customerregistrationimports' => $this->getcustomerregistrationimports(), 'headers' => $this->headers()]);
    }
}
