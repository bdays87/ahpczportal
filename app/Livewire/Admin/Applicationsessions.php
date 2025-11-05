<?php

namespace App\Livewire\Admin;

use App\Interfaces\iapplicationsessionInterface;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Mary\Traits\Toast;

class Applicationsessions extends Component
{
    use Toast;
  
    public $year;
    
  
    public $breadcrumbs = [];
    
    public $id;
    
    public $modal = false;
    protected  $repo;
    public function boot(iapplicationsessionInterface $repo)
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
                'label' => 'Application Sessions'
            ],
        ];
    }

    public function getapplicationsessions()
    {
        return $this->repo->getAll();
    }

    public function save()
{
    $this->validate([
        'year' => 'required'
    ]);
    if($this->id){
        $this->update();
    }else{
        $this->create();
    }
    
    $this->reset('year');
}

public function edit($id)
{
    $applicationsession = $this->repo->get($id);
    
    if (!$applicationsession) {
        $this->error('Application session not found.');
        return;
    }
    $this->year = $applicationsession->year;
    $this->id = $id;
    $this->modal = true;
}
public function create(){
    $response = $this->repo->create([
        'year' => $this->year,
        'user_id' => Auth::user()->id
       
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
        'year' => $this->year,
        'user_id' => Auth::user()->id
    ]);
    if ($response["status"] == "success") {
        $this->success($response["message"]);
    } else{
        $this->error($response["message"]);
    }
   
}
   

public function delete($id)
{
    $response = $this->repo->delete($id);
    if ($response['status'] == "success") {
        $this->success($response['message']);
    } else{
        $this->error($response['message']);
    }
}

public function headers():array{
    return [
        ['key' => 'year', 'label' => 'Year', 'sortable' => true],
        ['key' => 'user', 'label' => 'User', 'sortable' => false]
    ];
}
    public function render()
    {
        return view('livewire.admin.applicationsessions',[
            'applicationsessions' => $this->getapplicationsessions(),
            'headers' => $this->headers()
        ]);
    }
}
