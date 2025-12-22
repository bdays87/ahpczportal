<?php

namespace App\Livewire\Admin\Components;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Mary\Traits\Toast;
use App\Interfaces\idatamanagementInterface;
class Customeruserimports extends Component
{
    use Toast,WithFileUploads,WithPagination;

public $file;

public $search;

public $id;

public $modal = false;
public $type;

public $modifymodal = false;
public $importmodal = false;

 public $name;
 public $surname;
    public $regnumber;

 public $email;
 public $password;

protected $customerimportrepo;

public function boot(idatamanagementInterface $customerimportrepo)
{
    $this->customerimportrepo = $customerimportrepo;
}

public function getcustomerusers()
{
    return $this->customerimportrepo->getallusers($this->search);
}

public function saveimport()
{
    $this->validate([
        'file' => 'required|file|mimes:csv,txt',
    ]);
    $path = $this->file->store('customerimports');
    $response =  $this->customerimportrepo->importusers($path);
    
    if ($response['status'] == 'success') {
        $this->success($response['message']);
    } else {
        $this->error($response['message']);
    }
}

public function headers(): array
{
    return [   
        ['key' => 'id', 'label' => 'ID'],    
        ['key' => 'name', 'label' => 'Name'],
        ['key' => 'surname', 'label' => 'Surname'],
        ['key' => 'regnumber', 'label' => 'RegNumber'],
        ['key' => 'email', 'label' => 'Email'],
        ['key' => 'processed', 'label' => 'Processed'],
        ['key' => 'action', 'label' => ''],
    ];
}



public function save(){
    $this->validate([
        'name' => 'required',
        'surname' => 'required',
        'regnumber' => 'required',
        'password' => 'required',
        'email' => 'required'
    ]);
    if($this->id){
        $this->update();
    }else{
        $this->create();
    }
}

public function create(){
    $response = $this->customerimportrepo->createuser([
        'name' => $this->name,
        'surname' => $this->surname,
        'regnumber' => $this->regnumber,
        'email' => $this->email,
        'password' => $this->password,
    ]);
    if($response !=null){
    if ($response['status'] == 'success') {
        $this->success($response['message']);
    } else {
        $this->error($response['message']);
    }
    $this->reset(['name','surname','regnumber','email','password']);
    $this->modifymodal = false;
}
}

public function update(){
    $response = $this->customerimportrepo->updateuser($this->id, [
        'name' => $this->name,
        'surname' => $this->surname,
        'regnumber' => $this->regnumber,
        'email' => $this->email,
        'password' => $this->password,
    ]);
    if ($response['status'] == 'success') {
        $this->success($response['message']);
    } else {
        $this->error($response['message']);
    }
    $this->reset(['name','surname','regnumber','email','password']);
    $this->modifymodal = false;
}

public function edit($id){
    $this->id = $id;
    $customerimport = $this->customerimportrepo->getuser($id);
    $this->name = $customerimport->name;
    $this->surname = $customerimport->surname;
    $this->regnumber = $customerimport->regnumber;
    $this->email = $customerimport->email;
    $this->modifymodal = true;
}
public function delete($id){
    $response = $this->customerimportrepo->deleteuser($id);
    if ($response['status'] == 'success') {
        $this->success($response['message']);
    } else {
        $this->error($response['message']);
    }
}
    public function render()
    {
        return view('livewire.admin.components.customeruserimports', ['customerusers' => $this->getcustomerusers(), 'headers' => $this->headers()]);
    }
}
