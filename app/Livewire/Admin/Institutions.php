<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Mary\Traits\Toast;
use Livewire\WithPagination;
use App\Interfaces\institutionInterface;
class Institutions extends Component
{
    use Toast,WithPagination;

    public $search;
    public $name;
    public $accredited;
    public $id;
    public $modal = false;
    public $modifymodal = false;
    protected $repo;
    public $breadcrumbs = [
      
    ];
    public function mount()
    {
        $this->breadcrumbs = [
            [
                'label' => 'Dashboard',
                'icon' => 'o-home',
                'link' => route('dashboard'),
            ],
            ['label' => 'Institutions',
            ],
        ];
    }
    public function boot(institutionInterface $repo)
    {
        $this->repo = $repo;
    }
    public function getinstitutions()
    {
        return $this->repo->getAll($this->search);
    }
    public function UpdatedSearch(){
        $this->getinstitutions();
    }
    public function save(){
        $this->validate([
            'name' => 'required',
            'accredited' => 'required',
        ]);
        if($this->id){
            $this->update();
        }else{
            $this->create();
        }
    }
    public function create(){
        $response = $this->repo->create([
            'name' => $this->name,
            'accredited' => $this->accredited,
        ]);
    }
    public function update(){
        $response = $this->repo->update($this->id, [
            'name' => $this->name,
            'accredited' => $this->accredited,
        ]);
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
        $this->id = $id;
        $institution = $this->repo->get($id);
        if(!$institution){
            $this->error('Institution not found.');
            return;
        }
        $this->name = $institution->name;
        $this->accredited = $institution->accredited;
        $this->modal = true;
    }
    public function headers():array{
        return [
            ['key' => 'name', 'label' => 'Name'],
            ['key' => 'accredited', 'label' => 'Accredited'],
            ['key' => 'action', 'label' => ''],
        ];
    }
    public function render()
    {
        return view('livewire.admin.institutions', [
            'institutions' => $this->getinstitutions(),
            'headers' => $this->headers(),
        ]);
    }
}
