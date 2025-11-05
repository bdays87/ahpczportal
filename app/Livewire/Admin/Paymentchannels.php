<?php

namespace App\Livewire\Admin;

use App\Interfaces\icurrencyInterface;
use App\Interfaces\ipaymentchannelInterface;
use Livewire\Component;
use Livewire\WithFileUploads;
use Mary\Traits\Toast;

class Paymentchannels extends Component
{
    use Toast,WithFileUploads;
    
    public $name;
    public $file;
    public $status;
    public $id;
    public $key;
    public $value;
    public $currency_id;
    public $parameter_id;
    public $modal = false;
    public $parametermodal = false;
    public $breadcrumbs=[];
    public $paymentchannel = null;

    protected $repo;
    protected $currencyrepo;

    public function boot(ipaymentchannelInterface $repo, icurrencyInterface $currencyrepo)
    {
        $this->repo = $repo;
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
                'label' => 'Payment Channels'
            ],
        ];
    }

    public function getcurrencies()
    {
        return $this->currencyrepo->getAll('active');
    }
    public function getpaymentchannels()
    {
        return $this->repo->getAll();
    }
    public function save(){
        $this->validate([
            'name'=>'required',
            'status'=>'required'
        ]);
        if($this->id){
            $this->update();
        }else{
            $this->create();
        }
        $this->reset(['name','file','status','id']);
    }
    public function create(){
        $logo = null;
        if($this->file){
            $logo = $this->file->store('paymentchannels','public');
        }
       $response = $this->repo->create([
            'name'=>$this->name,
            'logo'=>$logo,
            'showpublic'=>$this->status
        ]);
        if($response['status']=='success'){
            $this->success($response['message']);
        }else{
            $this->error($response['message']);
        }
    }
    public function update(){
        $logo = null;
        if($this->file){
            $logo = $this->file->store('paymentchannels','public');
        }
        $response = $this->repo->update($this->id,[
            'name'=>$this->name,
            'logo'=>$logo,
            'showpublic'=>$this->status
        ]);
        if($response['status']=='success'){
            $this->success($response['message']);
        }else{
            $this->error($response['message']);
        }
       
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
        $paymentchannel = $this->repo->get($id);
        $this->name = $paymentchannel->name;
        $this->file = $paymentchannel->logo;
        $this->status = $paymentchannel->showpublic;
        $this->modal = true;
    }

    public function getparameters($id){
        $this->id = $id;
        $this->paymentchannel = $this->repo->getparameters($id);
        $this->parametermodal = true;
    }

    public function saveparameter(){
        $this->validate(['key'=>'required','value'=>'required','currency_id'=>'required']);
        if($this->parameter_id){
            $this->updateparameter();
        }else{
            $this->createparameter();
        }
        $this->reset(['key','value','currency_id','parameter_id']);
    }

    public function createparameter(){
        $response = $this->repo->createparameter([
            'key'=>$this->key,
            'value'=>$this->value,
            'currency_id'=>$this->currency_id,
            'paymentchannel_id'=>$this->id
        ]);
        if($response['status']=='success'){
            $this->success($response['message']);
        }else{
            $this->error($response['message']);
        }
    }

    public function updateparameter(){
        $response = $this->repo->updateparameter($this->parameter_id, [
            'key'=>$this->key,
            'value'=>$this->value,
            'currency_id'=>$this->currency_id,
            'paymentchannel_id'=>$this->id
        ]);
        if($response['status']=='success'){
            $this->success($response['message']);
        }else{
            $this->error($response['message']);
        }
    }

    public function deleteparameter($id){
        $response = $this->repo->deleteparameter($id);
        if($response['status']=='success'){
            $this->success($response['message']);
        }else{
            $this->error($response['message']);
        }
    }
    public function editparameter($id){
        $this->parameter_id = $id;
        $parameter = $this->repo->getparameter($id);
        $this->key = $parameter->key;
        $this->value = $parameter->value;
        $this->currency_id = $parameter->currency_id;
        $this->parametermodal = true;
    }

    public function headers():array{
        return [
            ['key'=>'logo', 'label'=>'Logo'],
            ['key'=>'name', 'label'=>'Name'],         
            ['key'=>'showpublic', 'label'=>'Show Public'],
            ['key'=>'action', 'label'=>'']
        ];
    }

    public function render()
    {
        return view('livewire.admin.paymentchannels',[
            'headers'=>$this->headers(),
            'records'=>$this->getpaymentchannels(),
            'currencies'=>$this->getcurrencies(),
            'paymentchannels'=>$this->getpaymentchannels()
        ]);
    }
}
