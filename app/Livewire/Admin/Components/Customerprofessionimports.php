<?php

namespace App\Livewire\Admin\Components;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Mary\Traits\Toast;
use App\Interfaces\idatamanagementInterface;
class Customerprofessionimports extends Component
{
    use Toast,WithFileUploads,WithPagination;
    public $file;
    public $search;
    public $id;
    public $importmodal = false;
    public $editmodal = false;

    public $tire_id;
    public $regnumber;
    public $prefix;
    public $status;
    protected $customerprofessionimportrepo;
    public function boot(idatamanagementInterface $customerprofessionimportrepo)
    {
        $this->customerprofessionimportrepo = $customerprofessionimportrepo;
    }
    public function getcustomerprofessionimports()
    {
        return $this->customerprofessionimportrepo->getallcustomerprofessions($this->search);
    }
    public function saveimport()
    {
        $this->validate([
            'file' => 'required|file|mimes:csv,txt',
        ]);
        $path = $this->file->store('customerprofessionimports');
        $response = $this->customerprofessionimportrepo->importcustomerprofessions($path);
        if ($response['status'] == 'success') {
            $this->success($response['message']);
        } else {
            $this->error($response['message']);
        }
    }
    public function savecustomerprofession()
    {
        $this->validate([
            'regnumber' => 'required',
            'prefix' => 'required',
            'status' => 'required',
        ]);
        if($this->id){
            $this->update();
        }else{
            $this->create();
        }
    }
    public function create(){
        $response = $this->customerprofessionimportrepo->createcustomerprofession([
            'regnumber' => $this->regnumber,
            'prefix' => $this->prefix,
            'status' => $this->status,
        ]);
        if ($response['status'] == 'success') {
            $this->success($response['message']);
        } else {
            $this->error($response['message']);
        }
    }
    public function update(){
        $response = $this->customerprofessionimportrepo->updatecustomerprofession($this->id, [
            'regnumber' => $this->regnumber,
            'prefix' => $this->prefix,
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
        $customerprofessionimport = $this->customerprofessionimportrepo->getcustomerprofession($id);
        $this->regnumber = $customerprofessionimport->regnumber;
        $this->prefix = $customerprofessionimport->prefix;
        $this->status = $customerprofessionimport->status;
        $this->editmodal = true;
    }
    public function delete($id)
    {
        $response = $this->customerprofessionimportrepo->deletecustomerprofession($id);
        if ($response['status'] == 'success') {
            $this->success($response['message']);
        } else {
            $this->error($response['message']);
        }
    }
    public function headers(): array
    {
        return [
            ['key'=>'regnumber','label'=>'RegNumber','sortable'=>true],
            ['key'=>'prefix','label'=>'Prefix','sortable'=>true],
            ['key'=>'status','label'=>'Status','sortable'=>true],
            ['key'=>'tire','label'=>'Tire','sortable'=>true],
            ['key'=>'customertype','label'=>'Customertype','sortable'=>true],
            ['key'=>'proceeded','label'=>'Proceeded','sortable'=>true],
            ['key'=>'action','label'=>'','sortable'=>false],
        ];
    }
    public function render()
    {
        return view('livewire.admin.components.customerprofessionimports', ['customerprofessionimports' => $this->getcustomerprofessionimports(), 'headers' => $this->headers()]);
    }
}
