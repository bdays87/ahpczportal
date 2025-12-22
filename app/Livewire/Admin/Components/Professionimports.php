<?php

namespace App\Livewire\Admin\Components;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Mary\Traits\Toast;
use App\Interfaces\idatamanagementInterface;

class Professionimports extends Component
{
    use Toast,WithFileUploads,WithPagination;

public $file;

public $search;

public $id;

public $modal = false;
public $editmodal = false;
public $name;
public $prefix;
protected $professionimportrepo;

public function boot(idatamanagementInterface $professionimportrepo)
{
    $this->professionimportrepo = $professionimportrepo;
}

public function getprofessionimports()
{
    return $this->professionimportrepo->getprofessionimports($this->search);
}

public function saveprofessionimport()
{
    $this->validate([
        'file' => 'required|file|mimes:csv,txt',
    ]);
    $path = $this->file->store('professionimports');
    $response = $this->professionimportrepo->saveprofessionimport($path);
    if ($response['status'] == 'success') {
        $this->success($response['message']);
    } else {
        $this->error($response['message']);
    }
}

public function saveprofession()
{
    $this->validate([
        'name' => 'required',
        'prefix' => 'required',
    ]);
    if($this->id){
        $this->update();
    }else{
        $this->create();
    }
}

public function edit($id)
{
    $this->id = $id;
    $professionimport = $this->professionimportrepo->getprofessionimport($id);
    $this->name = $professionimport->name;
    $this->prefix = $professionimport->prefix;
    $this->editmodal = true;
}

public function create(){
  $response = $this->professionimportrepo->createprofession([
        'name' => $this->name,
        'prefix' => $this->prefix,
    ]);
    if ($response['status'] == 'success') {
        $this->success($response['message']);
    } else {
        $this->error($response['message']);
    }
    $this->reset(['name','prefix']);
    $this->editmodal = false;
}
public function update(){
    $response = $this->professionimportrepo->updateprofessionimport($this->id, [
        'name' => $this->name,
        'prefix' => $this->prefix,
    ]);
    if ($response['status'] == 'success') {
        $this->success($response['message']);
    } else {
        $this->error($response['message']);
    }
    $this->reset(['name','prefix']);
    $this->editmodal = false;
}
public function delete($id)
{
    $response = $this->professionimportrepo->deleteprofessionimport($id);
    if ($response['status'] == 'success') {
        $this->success($response['message']);
    } else {
        $this->error($response['message']);
    }
}
public function headers(): array
{
    return [
        ['key'=>'name','label'=>'Name','sortable'=>true],
        ['key'=>'prefix','label'=>'Prefix','sortable'=>true],
        ['key'=>'proceeded','label'=>'Proceeded','sortable'=>true],
        ['key'=>'action','label'=>'','sortable'=>false],
    ];
}
    public function render()
    {
        return view('livewire.admin.components.professionimports', ['professionimports' => $this->getprofessionimports(), 'headers' => $this->headers()]);
    }
}
