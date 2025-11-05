<?php

namespace App\Livewire\Admin;

use App\Interfaces\icurrencyInterface;
use Livewire\Component;
use Mary\Traits\Toast;

class Currencies extends Component
{
    use Toast;
    public $name;
    public $status;
    public $id;
    public $modal = false;
    public $breadcrumbs=[];
    protected $currencyrepo;
    public function boot(icurrencyInterface $currencyrepo)
    {
        $this->currencyrepo = $currencyrepo;
    }
    public function mount(){
        $this->breadcrumbs = [
            [
                'label' => 'Dashboard',
                'icon' => 'o-home',
                'link' => route('dashboard'),
            ],
            [
                'label' => 'Currencies'
            ],
        ];
    }

    public function getcurrencies(){
        return $this->currencyrepo->getAll(null);
    }
    public function save(){
        $this->validate([
            'name' => 'required',
            'status' => 'required',
        ]);
        if($this->id){
            $this->update();
        }else{
            $this->create();
        }
        $this->reset(['name','status','id']);
    }
    public function create(){
        $response = $this->currencyrepo->create([
            'name' => $this->name,
            'status' => $this->status,
        ]);
        if ($response["status"] == "success") {
            $this->success($response["message"]);
        } else{
            $this->error($response["message"]);
        }
    }
    public function update(){
        $response = $this->currencyrepo->update($this->id,[
            'name' => $this->name,
            'status' => $this->status,
        ]);
        if ($response["status"] == "success") {
            $this->success($response["message"]);
        } else{
            $this->error($response["message"]);
        }
    }
    public function delete($id){
        $response = $this->currencyrepo->delete($id);
        if ($response['status'] == "success") {
            $this->success($response['message']);
        } else{
            $this->error($response['message']);
        }
    }
    public function edit($id){
        $currency = $this->currencyrepo->get($id);
        if (!$currency) {
            $this->error('Currency not found.');
            return;
        }
        $this->name = $currency->name;
        $this->status = $currency->status;
        $this->id = $id;
        $this->modal = true;
    }

    public function headers():array{
        return [
            ['key' => 'name', 'label' => 'Name'],
            ['key' => 'status', 'label' => 'Status']
        ];
    }

    public function render()
    {
        return view('livewire.admin.currencies',[
            'currencies' => $this->getcurrencies(),
            'headers' => $this->headers()
        ]);
    }
}
