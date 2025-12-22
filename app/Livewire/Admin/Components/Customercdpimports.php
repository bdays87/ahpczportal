<?php

namespace App\Livewire\Admin\Components;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Mary\Traits\Toast;
use App\Interfaces\idatamanagementInterface;

class Customercdpimports extends Component
{
    use Toast,WithFileUploads,WithPagination;
    public $file;
    public $search;
    public $id;
    public $importmodal = false;
    public $editmodal = false;
    public $regnumber;
    public $points;
    public $year;
    protected $customercdpimportrepo;
    public function boot(idatamanagementInterface $customercdpimportrepo)
    {
        $this->customercdpimportrepo = $customercdpimportrepo;
    }
    public function getcustomercdps()
    {
        return $this->customercdpimportrepo->getallcustomercdps($this->search);
    }
    public function headers(): array
    {
        return [
            ['key'=>'regnumber','label'=>'RegNumber','sortable'=>true],
            ['key'=>'points','label'=>'Points','sortable'=>true],
            ['key'=>'year','label'=>'Year','sortable'=>true],
        ];
    }
    public function saveimport()
    {
        $this->validate([
            'file' => 'required|file|mimes:csv,txt',
        ]);
        $path = $this->file->store('customercdpimports');
        $response = $this->customercdpimportrepo->importcustomercdps($path);
        if ($response['status'] == 'success') {
            $this->success($response['message']);
        } else {
            $this->error($response['message']);
        }
    }
    public function savecustomercdp()
    {
        $this->validate([
            'regnumber' => 'required|string|max:255',
            'points' => 'required|string|max:255',
            'year' => 'required|string|max:255',
        ]);
        if($this->id){
            $this->update();
        }else{
            $this->create();
        }
        $this->reset('regnumber','points','year','id','editmodal');
    }
    public function create()
    {
        $response = $this->customercdpimportrepo->createcustomercdp([
            'regnumber' => $this->regnumber,
            'points' => $this->points,
            'year' => $this->year,
        ]);
        if ($response['status'] == 'success') {
            $this->success($response['message']);
        } else {
            $this->error($response['message']);
        }
    }
    public function update()
    {
        $response = $this->customercdpimportrepo->updatecustomercdp($this->id,['regnumber' => $this->regnumber,
            'points' => $this->points,
            'year' => $this->year,
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
        $customercdp = $this->customercdpimportrepo->getcustomercdp($id);
        $this->regnumber = $customercdp->regnumber;
        $this->points = $customercdp->points;
        $this->year = $customercdp->year;
    }
    public function render()
    {
        return view('livewire.admin.components.customercdpimports', ['customercdps' => $this->getcustomercdps(), 'headers' => $this->headers()]);
    }
}
