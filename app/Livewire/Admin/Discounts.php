<?php

namespace App\Livewire\Admin;

use App\Interfaces\idiscountInterface;
use App\Interfaces\itireInterface;
use Livewire\Component;
use Mary\Traits\Toast;

class Discounts extends Component
{
    use Toast;
    public $tire_id;
    public $lowerlimit;
    public $upperlimit;
    public $discount;
    public $id;
    public $modal = false;
    public $breadcrumbs=[];
    protected $tirerepo;
    protected $repo;
    public function boot(itireInterface $tirerepo, idiscountInterface $repo)
    {
        $this->tirerepo = $tirerepo;
        $this->repo = $repo;
    }

    public function mount(){
        $this->breadcrumbs=[
            [
                'label' => 'Dashboard',
                'icon' => 'o-home',
                'link' => route('dashboard'),
            ],
            [
                'label' => 'Discounts'
            ],
        ];
    }

    public  function gettires()
    {
        return $this->tirerepo->getAll();
    }

    public function getdiscounts()
    {
        return $this->repo->getAll();
    }

    public function save(){
        $this->validate([
            'tire_id' => 'required',
            'lowerlimit' => 'required',
            'upperlimit' => 'required |gt:lowerlimit',
            'discount' => 'required',
        ]);
        if($this->id){
            $this->update();
        }else{
            $this->create();
        }
        $this->reset(['tire_id', 'lowerlimit', 'upperlimit', 'discount']);
    }

    public function create(){
        $response = $this->repo->create([
            'tire_id' => $this->tire_id,
            'lowerlimit' => $this->lowerlimit,
            'upperlimit' => $this->upperlimit,
            'discount' => $this->discount,
        ]);
        if($response['status']==='success'){
            $this->success($response['message']);
        }else{
            $this->error($response['message']);
        }

    }

    public function update(){
        $response = $this->repo->update($this->id, [
            'tire_id' => $this->tire_id,
            'lowerlimit' => $this->lowerlimit,
            'upperlimit' => $this->upperlimit,
            'discount' => $this->discount,
        ]);
        if($response['status']==='success'){
            $this->success($response['message']);
        }else{
            $this->error($response['message']);
        }
    }
    public function delete(){
        $response = $this->repo->delete($this->id);
        if($response['status']==='success'){
            $this->success($response['message']);
        }else{
            $this->error($response['message']);
        }
    }

    public function edit($id){
        $penality = $this->repo->get($id);
        $this->id = $penality->id;
        $this->tire_id = $penality->tire_id;
        $this->lowerlimit = $penality->lowerlimit;
        $this->upperlimit = $penality->upperlimit;
        $this->discount = $penality->discount;
        $this->modal = true;
    }

    public function headers():array{

        return [
            ['key' => 'tire.name', 'label' => 'Tire'],
            ['key' => 'lowerlimit', 'label' => 'Lower Limit'],
            ['key' => 'upperlimit', 'label' => 'Upper Limit'],
            ['key' => 'discount', 'label' => 'Discount'],
            ['key' => 'action', 'label' => ''],
        ];
    }
    public function render()
    {
        return view('livewire.admin.discounts', [
            'discounts' => $this->getdiscounts(),
            'tires' => $this->gettires(),
            'headers' => $this->headers(),
        ]);
    }
}
