<?php

namespace App\Livewire\Admin;

use App\Interfaces\icurrencyInterface;
use App\Interfaces\iexchangerateInterface;
use Livewire\Component;
use Mary\Traits\Toast;

class Exchangerates extends Component
{
    use Toast;
    public $base_currency_id;
    public $secondary_currency_id;
    public $rate;
    public $start_date;
    public $end_date;
    public $year;
    public $id;
    public $modal=false;
    public $breadcrumbs=[];
    protected $exchangerateRepository;
    protected $currencyRepository;
    public function boot(iexchangerateInterface $exchangerateRepository, icurrencyInterface $currencyRepository)
    {
        $this->exchangerateRepository = $exchangerateRepository;
        $this->currencyRepository = $currencyRepository;
    }

    public function getcurrencies(){
        return $this->currencyRepository->getAll(null);
    }

    public function mount()
    {
        $this->breadcrumbs = [  [
            'label' => 'Dashboard',
            'icon' => 'o-home',
            'link' => route('dashboard'),
        ],
        [
            'label' => 'Exchange Rates'
        ]
    ];
    $this->year = now()->year;
    }
    public function getexchangerates(){
        return $this->exchangerateRepository->getAll($this->year);
    }

    public function save(){
        $this->validate([
            "base_currency_id"=>"required",
            "secondary_currency_id"=>"required",
            "rate"=>"required",
            "start_date"=>"required",
            "end_date"=>"required"
        ]);
        if($this->id){
            $this->update();
        }else{
            $this->create();
        }
        $this->reset([
            "base_currency_id",
            "secondary_currency_id",
            "rate",
            "start_date",
            "end_date"
        ]);
    }

    public function update(){
      $response =  $this->exchangerateRepository->update($this->id, [
            "base_currency_id"=>$this->base_currency_id,
            "secondary_currency_id"=>$this->secondary_currency_id,
            "rate"=>$this->rate,
            "start_date"=>$this->start_date,
            "end_date"=>$this->end_date
        ]);
        if($response["status"] == "success"){
            $this->success($response["message"]);
        }else{
            $this->error($response["message"]);
        }
    }

    public function create(){
      $response =  $this->exchangerateRepository->create([
            "base_currency_id"=>$this->base_currency_id,
            "secondary_currency_id"=>$this->secondary_currency_id,
            "rate"=>$this->rate,
            "start_date"=>$this->start_date,
            "end_date"=>$this->end_date
        ]);
        if($response["status"] == "success"){
            $this->success($response["message"]);
        }else{
            $this->error($response["message"]);
        }
    }
    public function delete($id){
      $response =  $this->exchangerateRepository->delete($id);
      if($response["status"] == "success"){
          $this->success($response["message"]);
      }else{
          $this->error($response["message"]);
      }
    }
    public function edit($id){
        $this->id = $id;
        $rate = $this->exchangerateRepository->get($id);
        $this->base_currency_id = $rate->base_currency_id;
        $this->secondary_currency_id = $rate->secondary_currency_id;
        $this->rate = $rate->rate;
        $this->start_date = $rate->start_date;
        $this->end_date = $rate->end_date;
        $this->modal = true;
    }

    public function headers():array{
        return [
            ["key"=>"basecurrency.name", "label"=>"Base Currency"],
            ["key"=>"secondarycurrency.name", "label"=>"Secondary Currency"],
            ["key"=>"rate", "label"=>"Rate"],
            ["key"=>"start_date", "label"=>"Start Date"],
            ["key"=>"end_date", "label"=>"End Date"],
            ["key"=>"user.name", "label"=>"User"],
            ["key"=>"actions", "label"=>""]
        ];
    }
    public function render()
    {
        return view('livewire.admin.exchangerates', [
            "exchangerates"=>$this->getexchangerates(),
            "headers"=>$this->headers(),
            "currencies"=>$this->getcurrencies()
        ]);
    }
}
