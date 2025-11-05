<?php

namespace App\Livewire\Admin;

use App\interfaces\iaccounttypeInterface;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Mary\Traits\Toast;

class Accounttypes extends Component
{
    use Toast;
    #[Rule("required")]
    public $name;
    
  
    public $breadcrumbs = [];
    
    public $id;
    
    public $modal = false;
    protected  $repo;
    public function boot(iaccounttypeInterface $repo)
    {
        $this->repo = $repo;
    }
    public function mount()
    {
        $this->breadcrumbs = [
            [
                'label' => 'Dashboard',
                'icon' => 'o-home',
                'link' => route('dashboard'),
            ],
            [
                'label' => 'Account Types'
            ],
        ];
    }

    public function getaccounttypes()
    {
        return $this->repo->getAll();
    }

    public function save()
{
    $this->validate([
        'name' => 'required'
    ]);
    if($this->id){
        $this->update();
    }else{
        $this->create();
    }
    
    $this->reset(['name']);
}

public function edit($id)
{
    $accounttype = $this->repo->get($id);
    
    if (!$accounttype) {
        $this->error('Account type not found.');
        return;
    }
    $this->name = $accounttype->name;
    $this->id = $id;
    $this->modal = true;
}
public function create(){
    $response = $this->repo->create([
        'name' => $this->name
       
    ]);
    if ($response["status"] == "success") {
        $this->success($response["message"]);
    } else{
        $this->error($response["message"]);
    }
    
   
}
public function update()
{
 
    $response = $this->repo->update($this->id,[
        'name' => $this->name,
    ]);
    if ($response["status"] == "success") {
        $this->success($response["message"]);
    } else{
        $this->error($response["message"]);
    }
   
}
   

public function delete($id)
{
    $response = $this->repo->deleteaccounttype($id);
    if ($response['status'] == "success") {
        $this->success($response['message']);
    } else{
        $this->error($response['message']);
    }
}

public function headers():array{
    return [
        ['key' => 'name', 'label' => 'Name']
    ];
}
    public function render()
    {
        return view('livewire.admin.accounttypes',[
            'accounttypes' => $this->getaccounttypes(),
            'headers' => $this->headers()
        ]);
    }
}
