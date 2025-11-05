<?php

namespace App\Livewire\Admin;

use App\Interfaces\ipenaltiesInterface;
use App\Interfaces\itireInterface;
use Livewire\Component;
use Mary\Traits\Toast;

class Penalities extends Component
{
    use Toast;
    public $tire_id;
    public $lowerlimit;
    public $upperlimit;
    public $penality;
    public $id;
    public $modal = false;
    public $breadcrumbs=[];
    protected $tirerepo;
    protected $penalityrepo;
    public function boot(itireInterface $tirerepo, ipenaltiesInterface $penalityrepo)
    {
        $this->tirerepo = $tirerepo;
        $this->penalityrepo = $penalityrepo;
    }

    public function mount(){
        $this->breadcrumbs=[
            [
                'label' => 'Dashboard',
                'icon' => 'o-home',
                'link' => route('dashboard'),
            ],
            [
                'label' => 'Penalities'
            ],
        ];
    }

    public  function gettires()
    {
        return $this->tirerepo->getAll();
    }

    public function getpenalities()
    {
        return $this->penalityrepo->getAll();
    }

    public function save(){
        $this->validate([
            'tire_id' => 'required',
            'lowerlimit' => 'required',
            'upperlimit' => 'required |gt:lowerlimit',
            'penality' => 'required',
        ]);
        if($this->id){
            $this->update();
        }else{
            $this->create();
        }
        $this->reset(['tire_id', 'lowerlimit', 'upperlimit', 'penality']);
    }

    public function create(){
        $response = $this->penalityrepo->create([
            'tire_id' => $this->tire_id,
            'lowerlimit' => $this->lowerlimit,
            'upperlimit' => $this->upperlimit,
            'penalty' => $this->penality,
        ]);
        if($response['status']==='success'){
            $this->success($response['message']);
        }else{
            $this->error($response['message']);
        }

    }

    public function update(){
        $response = $this->penalityrepo->update($this->id, [
            'tire_id' => $this->tire_id,
            'lowerlimit' => $this->lowerlimit,
            'upperlimit' => $this->upperlimit,
            'penalty' => $this->penality,
        ]);
        if($response['status']==='success'){
            $this->success($response['message']);
        }else{
            $this->error($response['message']);
        }
    }
    public function delete(){
        $response = $this->penalityrepo->delete($this->id);
        if($response['status']==='success'){
            $this->success($response['message']);
        }else{
            $this->error($response['message']);
        }
    }

    public function edit($id){
        $penality = $this->penalityrepo->get($id);
        $this->id = $penality->id;
        $this->tire_id = $penality->tire_id;
        $this->lowerlimit = $penality->lowerlimit;
        $this->upperlimit = $penality->upperlimit;
        $this->penality = $penality->penalty;
        $this->modal = true;
    }

    public function headers():array{

        return [
            ['key' => 'tire.name', 'label' => 'Tire'],
            ['key' => 'lowerlimit', 'label' => 'Lower Limit'],
            ['key' => 'upperlimit', 'label' => 'Upper Limit'],
            ['key' => 'penalty', 'label' => 'Penality'],
            ['key' => 'action', 'label' => ''],
        ];
    }
    public function render()
    {
        return view('livewire.admin.penalities',[
            'penalities' => $this->getpenalities(),
            'tires' => $this->gettires(),
            'headers' => $this->headers(),
        ]);
    }
}
