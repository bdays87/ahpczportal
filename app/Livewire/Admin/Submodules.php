<?php

namespace App\Livewire\Admin;

use App\Interfaces\isubmoduleInterface;
use App\Interfaces\isystemmoduleInterface;
use Livewire\Component;
use Mary\Traits\Toast;

class Submodules extends Component
{
    use Toast;
    public $systemmodule_id;
    public $name;
    public $url;
    public $icon;
    public $id;
    public $default_permission;
    public $modal = false;
    protected $repo;
    protected $systemmodulerepo;
    public $breadcrumbs = [];
    public function boot(isubmoduleInterface $repo, isystemmoduleInterface $systemmodulerepo)
    {
        $this->repo = $repo;
        $this->systemmodulerepo = $systemmodulerepo;
    }
    public function mount($systemmodule_id){
        $this->systemmodule_id = $systemmodule_id;
        $this->breadcrumbs = [
            [
                'label' => 'Dashboard',
                'icon' => 'o-home',
                'link' => route('dashboard'),
            ],
            [
                'label' => 'System Modules',
                'icon' => 'o-link',
                'link' => route('systemmodules.index'),
            ],
            [
                'label' => 'Sub Modules'
            ],
        ];
    }
    public function getSystemModule()
    {
        return $this->systemmodulerepo->getSubmodules($this->systemmodule_id);
    }
  
    public function headers():array{
        return [
            ['key' => 'name', 'label' => 'Name'],
            ['key' => 'url', 'label' => 'Url'],
            ['key' => 'default_permission', 'label' => 'Permission'],
            ['key' => 'icon', 'label' => 'Icon']
        ];
    }

    public function save(){
        $this->validate([ 
            'name' => 'required',
            'url' => 'required',
            'icon' => 'required',
            'default_permission' => 'required',
        ]);
        if($this->id){
            $this->update();
        }else{
            $this->create();
        }
        $this->reset(['name','url','icon','id','default_permission']);
    }

    public function create(){
        $response = $this->repo->create([
            'name' => $this->name,
            'url' => $this->url,
            'icon' => $this->icon,
            'default_permission' => $this->default_permission,
            'systemmodule_id' => $this->systemmodule_id,
        ]);
        if ($response["status"] == "success") {
            $this->success($response["message"]);
        } else{
            $this->error($response["message"]);
        }
    }
    public function update(){
        $response = $this->repo->update($this->id, [
            'name' => $this->name,
            'url' => $this->url,
            'default_permission' => $this->default_permission,
            'icon' => $this->icon,
            'systemmodule_id' => $this->systemmodule_id,
        ]);
        if ($response["status"] == "success") {
            $this->success($response["message"]);
        } else{
            $this->error($response["message"]);
        }
    }
    public function delete($id){
        $response = $this->repo->delete($id);
        if ($response['status'] == "success") {
            $this->success($response['message']);
        } else{
            $this->error($response['message']);
        }
    }
    public function edit($id){
        $submodule = $this->repo->get($id);
        if (!$submodule) {
            $this->error('Submodule not found.');
            return;
        }
        $this->name = $submodule->name;
        $this->url = $submodule->url;
        $this->default_permission = $submodule->default_permission;
        $this->icon = $submodule->icon;
        $this->systemmodule_id = $submodule->systemmodule_id;
        $this->id = $id;
        $this->modal = true;
    }   
    public function render()
    {
        return view('livewire.admin.submodules',[
            'systemmodule' => $this->getSystemModule(),
            'headers' => $this->headers()
        ]);
    }
}
