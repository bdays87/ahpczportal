<?php

namespace App\Livewire\Admin\Components;

use App\Interfaces\icustomeremploymentInteface;
use Livewire\Component;
use Livewire\Traits\WithPagination;
use Mary\Traits\Toast;

class Employmentdetails extends Component
{
    use Toast;
    public $companyname;
    public $position;
    public $start_date;
    public $end_date;
    public $phone;
    public $email;
    public $address;
    public $contactperson;
    public $customer;
    public  $currentemployment=false;
    public $id;
    public $modal=false;
    protected $customeremploymentRepository;
    public function boot(icustomeremploymentInteface $customeremploymentRepository)
    {
        $this->customeremploymentRepository = $customeremploymentRepository;
    }
    public function mount($customer){
        $this->customer = $customer;
    }

    public function save(){
        $this->validate([
            'companyname' => 'required',
            'position' => 'required',
            'start_date' => 'required',
            'end_date' => 'required if:currentemployment,false',
            'phone' => 'required',
            'email' => 'required',
            'address' => 'required',
            'contactperson' => 'required',
        ]);
        if($this->id){
             $this->update();
        }else{
            $this->create();
        }
        $this->reset(['companyname', 'position', 'start_date', 'end_date', 'phone', 'email', 'address', 'contactperson','id']);
    }


    public function create(){
        $response = $this->customeremploymentRepository->create([
            'companyname' => $this->companyname,
            'position' => $this->position,
            'start_date' => $this->start_date,
            'end_date' => $this->currentemployment ? 'CURRENT' : $this->end_date,
            'phone' => $this->phone,
            'email' => $this->email,
            'address' => $this->address,
            'contactperson' => $this->contactperson,
            'customer_id' => $this->customer->id,
        ]);
        if($response['status'] == 'success'){
            $this->success($response['message']);
        }else{
            $this->error($response['message']);
        }

    }

    public function update(){
        $response = $this->customeremploymentRepository->update($this->id, [
            'companyname' => $this->companyname,
            'position' => $this->position,
            'start_date' => $this->start_date,
            'end_date' => $this->currentemployment ? 'CURRENT' : $this->end_date,
            'phone' => $this->phone,
            'email' => $this->email,
            'address' => $this->address,
            'contactperson' => $this->contactperson,
            'customer_id' => $this->customer->id,
        ]);
        if($response['status'] == 'success'){
            $this->success($response['message']);
        }else{
            $this->error($response['message']);
        }
    }
    public function delete($id){
        $response = $this->customeremploymentRepository->delete($id);
        if($response['status'] == 'success'){
            $this->success($response['message']);
        }else{
            $this->error($response['message']);
        }
    }

    public function edit($id){
        $this->id = $id;
        $payload = $this->customeremploymentRepository->get($id);
        $this->companyname = $payload->companyname;
        $this->position = $payload->position;
        $this->start_date = $payload->start_date;
        $this->end_date = $payload->end_date;
        $this->phone = $payload->phone;
        $this->email = $payload->email;
        $this->address = $payload->address;
        $this->contactperson = $payload->contactperson;
        $this->modal = true;
    }
    public function headers():array{
        return [
            ["key"=>"companyname", "label"=>"Company Name"],
            ["key"=>"position", "label"=>"Position"],
            ["key"=>"dates", "label"=>"Dates"],
            ["key"=>"phone", "label"=>"Phone"],
            ["key"=>"email", "label"=>"Email"],
            ["key"=>"address", "label"=>"Address"],
            ["key"=>"contactperson", "label"=>"Contact Person"],
        ];
    }
    public function render()
    {
        return view('livewire.admin.components.employmentdetails', [
            'headers' => $this->headers()
        ]);
    }
}
