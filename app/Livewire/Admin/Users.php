<?php

namespace App\Livewire\Admin;

use App\Interfaces\iaccounttypeInterface;
use App\Interfaces\iuserInterface;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

class Users extends Component
{
    use Toast,WithPagination;

    public $name;
    public $surname;
    public $email;
    public $phone;
    public $status;
    public $modal = false;
    public $id;
    public $accounttype_id;
    public $accounttypefilter;
    public $breadcrumbs=[];
    public $search = '';
    protected $repo;
    protected $accounttyperepo;
    public function boot(iuserInterface $repo,iaccounttypeInterface $accounttyperepo){
        $this->repo = $repo;
        $this->accounttyperepo = $accounttyperepo;
    }
    public function mount(){
        $this->breadcrumbs = [
            [
                'label' => 'Dashboard',
                'icon' => 'o-home',
                'link' => route('dashboard'),
            ],
            [
                'label' => 'Users'
            ]
        ];
    }    
    public function getusers(){
        return $this->repo->getAll($this->search,$this->accounttypefilter);
    }
    public function getaccounttypes(){
        return $this->accounttyperepo->getAll();
    }
    public function save(){
        $this->validate([
            'name' => 'required',
            'surname' => 'required',
            'email' => 'required|email',
            'phone' => 'required',
            'accounttype_id' => 'required',
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
            'surname' => $this->surname,
            'email' => $this->email,
            'phone' => $this->phone,
            'accounttype_id' => $this->accounttype_id,
        ]);
        if($response['status'] == 'success'){
            $this->success($response['message']);
            $this->modal = false;
            $this->reset();
        }else{
            $this->error($response['message']);
        }
    }
    public function update(){
        $this->validate([
         'status' => 'required',
        ]);
        $response = $this->repo->update($this->id, [
            'name' => $this->name,
            'surname' => $this->surname,
            'email' => $this->email,
            'phone' => $this->phone,
            'status' => $this->status,
            'accounttype_id' => $this->accounttype_id,
        ]);
        if($response['status'] == 'success'){
            $this->success($response['message']);
            $this->modal = false;
            $this->reset();
        }else{
            $this->error($response['message']);
        }
    }
    public function delete($id){
        $response = $this->repo->delete($id);
        if($response['status'] == 'success'){
            $this->success($response['message']);
        }else{
            $this->error($response['message']);
        }
    }
    public function edit($id){
        $this->id = $id;
        $user = $this->repo->get($id);
        if(!$user){
            $this->error('User not found');
            return;
        }
        $this->name = $user->name;
        $this->surname = $user->surname;
        $this->email = $user->email;
        $this->phone = $user->phone;
        $this->status = $user->status;
        $this->accounttype_id = $user->accounttype_id;
        $this->modal = true;
    }

    public function headers():array{
        return [
            ["key"=>"name", "label"=>"Name"],
            ["key"=>"surname", "label"=>"Surname"],
            ["key"=>"email", "label"=>"Email"],
            ["key"=>"phone", "label"=>"Phone"],
            ["key"=>"status", "label"=>"Status"],
            ["key"=>"accounttype.name", "label"=>"Account Type"],
            ["key"=>"actions", "label"=>""]
        ];
    }
    public function render()
    {
        return view('livewire.admin.users', [
            'users' => $this->getusers(),
            'headers' => $this->headers(),
            'accounttypes' => $this->getaccounttypes()
        ]);
    }
}
