<?php

namespace App\Livewire\Admin;

use App\Interfaces\iaccounttypeInterface;
use App\Interfaces\isystemmoduleInterface;
use Livewire\Component;
use Mary\Traits\Toast;

class Systemmodules extends Component
{
    use Toast;
    public $name;
    public $accounttype_id;
    public $icon;
    public $default_permission;
    public $id;
    public $modal = false;
    protected $repo;
    protected $accounttyperepo;
    public $breadcrumbs = [];

    public function boot(iaccounttypeInterface $accounttyperepo,isystemmoduleInterface $repo)
    {
        $this->accounttyperepo = $accounttyperepo;
        $this->repo = $repo;
    }
    public function mount(){
        $this->breadcrumbs = [
            [
                'label' => 'Dashboard',
                'icon' => 'o-home',
                'link' => route('dashboard'),
            ],
            [
                'label' => 'System Modules'
            ],
        ];
    }

    public function getaccounttypes()
    {
        return $this->accounttyperepo->getAll();
    }

    public function getmodules()
    {
        return $this->repo->getAll();
    }

    public function save(){
        $this->validate([
            'name' => 'required',
            'accounttype_id' => 'required',
            'icon' => 'required',
            'default_permission' => 'required',
        ]);
        if($this->id){
            $this->update();
        }else{
            $this->create();
        }
        $this->reset(['name','accounttype_id','icon','default_permission','id']);
    }
    public function create(){
        $response = $this->repo->create([
            'name' => $this->name,
            'accounttype_id' => $this->accounttype_id,
            'icon' => $this->icon,
            'default_permission' => $this->default_permission,
        ]);
        if ($response["status"] == "success") {
            $this->success($response["message"]);
        } else{
            $this->error($response["message"]);
        }
    }
    public function update(){
        $response = $this->repo->update($this->id,[
            'name' => $this->name,
            'accounttype_id' => $this->accounttype_id,
            'icon' => $this->icon,
            'default_permission' => $this->default_permission,
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
        $module = $this->repo->get($id);
        if (!$module) {
            $this->error('Module not found.');
            return;
        }
        $this->name = $module->name;
        $this->accounttype_id = $module->accounttype_id;
        $this->icon = $module->icon;
        $this->default_permission = $module->default_permission;
        $this->id = $id;
        $this->modal = true;
    }
    public function headers():array{
        return [
            ['key' => 'name', 'label' => 'Name'],
            ['key' => 'accounttype.name', 'label' => 'Account Type'],
            ['key' => 'icon', 'label' => 'Icon'],
            ['key' => 'default_permission', 'label' => 'Default Permission']
        ];
    }
    public function render()
    {
        return view('livewire.admin.systemmodules',[
            'modules' => $this->getmodules(),
            'headers' => $this->headers(),
            'accounttypes' => $this->getaccounttypes()
        ]);
    }
}
