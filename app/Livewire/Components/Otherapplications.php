<?php

namespace App\Livewire\Components;

use Livewire\Component;
use App\Interfaces\iotherapplicationInterface;
use App\Interfaces\iotherserviceInterface;
use App\Interfaces\iapplicationsessionInterface;
use Mary\Traits\Toast;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Barryvdh\DomPDF\Facade\Pdf;
class Otherapplications extends Component
{
    use Toast;
    public $customer;
    public $modal = false;
    public $otherservice_id;
    public $tradename;
    public $year;
    public $profession_id;
    public $period;
    public $search;
    public $id;
    public $selectedotherservice;
    protected $otherapplicationrepo;
    protected $otherservicerepo;

    protected $sessionrepo;
    public function boot(iotherapplicationInterface $otherapplicationrepo,iotherserviceInterface $otherservicerepo,iapplicationsessionInterface $sessionrepo){
        $this->otherapplicationrepo = $otherapplicationrepo;
        $this->otherservicerepo = $otherservicerepo;
        $this->sessionrepo = $sessionrepo;
    }
    public function mount($customer){
        $this->customer = $customer;
        $this->year = date('Y');
    }
    public function updatedOtherserviceid(){
       $payload = $this->otherservicerepo->get($this->otherservice_id);
      
       $this->selectedotherservice = $payload;
    }
    public function getotherservices(){
        return $this->otherservicerepo->getAll($this->search);
    }
    public function getotherapplications(){
        $payload= $this->otherapplicationrepo->getbycustomer($this->customer->id,$this->year);
       
        return $payload;
    }
    public function getsessions(){
        return $this->sessionrepo->getAll();
    }

    public function professionlist(){
        $customerprofessions = $this->customer->customerprofessions;
        $professions = [];
        foreach($customerprofessions as $customerprofession){
            if($customerprofession->year>=$this->year && $customerprofession->status=="APPROVED" && $customerprofession->registertype?->name=="Main" && $customerprofession->customertype->name=="Practitioner"){
                $professions[] = ['id'=>$customerprofession->id,'name'=>$customerprofession->profession->name];
            }
        }
        return $professions;
    }

    public function save(){
        $this->validate([
            'otherservice_id' => 'required',
            'period' => 'required'
        ]);
        if($this->selectedotherservice?->requireapproval=="YES"){
            $this->validate([
                'profession_id' => 'required',
            ]);
        }
        if($this->id){
            $this->update();
        }else{
            $this->create();
        }
    }

    public function edit($id){
        $this->id = $id;
        $this->otherapplication = $this->otherapplicationrepo->getbyid($id);
        $this->otherservice_id = $this->otherapplication->otherservice_id;
        $this->period = $this->otherapplication->period;
        $this->profession_id = $this->otherapplication->customerprofession_id;
        $this->tradename = $this->otherapplication->tradename;
        $this->updatedOtherserviceid();
        $this->modal = true;
    }
    public function create(){
        $response = $this->otherapplicationrepo->create([
            'customer_id' => $this->customer->id,
            'otherservice_id' => $this->otherservice_id,
            'period' => $this->period,
            'customerprofession_id' => $this->profession_id,
            'tradename' => $this->tradename,
        ]);
        if($response['status']=="success"){
            $this->success($response['message']);
            $this->redirect(route('otherapplications.show', $response['uuid']));
            $this->modal = false;
        }else{
            $this->error($response['message']);
        }
        $this->reset('otherservice_id','period','profession_id');
    }
    public function update(){
        $response = $this->otherapplicationrepo->update($this->id,[
            'customer_id' => $this->customer->id,
            'otherservice_id' => $this->otherservice_id,
            'period' => $this->period,
            'customerprofession_id' => $this->profession_id,
            'tradename' => $this->tradename,
        ]);
        if($response['status']=="success"){
            $this->success($response['message']);
            $this->redirect(route('otherapplications.show', $response['uuid']));
            $this->modal = false;
        }else{
            $this->error($response['message']);
        }
        $this->reset('otherservice_id','period','profession_id');
        $this->id = null;
    }

    public function delete($id){
        $response = $this->otherapplicationrepo->delete($id);
        if($response['status']=="success"){
            $this->success($response['message']);
        }else{
            $this->error($response['message']);
        }
    }

    
    public function downloadcertificate($uuid){
        try{
            $otherapplication = $this->otherapplicationrepo->getbyuuid($uuid);
            $data = $otherapplication["data"];
            $qrcodeSvg = QrCode::size(300) 
            ->format('svg')
            ->generate("Institution Name:".$data->tradename."\n Certificate Number:".$data->certificate_number."\n expire date:".$data->certificate_expiry_date."\n verification link:".config('generalutils.base_url').'/verifications/other/'.$data->certificate_number);
        
        // Generate PDF with the QR code as a data URI
        if(config('generalutils.client') == "MLCSCZ"){
            $pdf = Pdf::loadView('certificates.other.mlcscz', [
                'qrcode' => 'data:image/svg+xml;base64,' . base64_encode($qrcodeSvg),
                'data'=>$data
            ]);
        }else if(config('generalutils.client') == "AHPCZ"){
            $pdf = Pdf::loadView('certificates.other.ahpcz', [
                'qrcode' => 'data:image/svg+xml;base64,' . base64_encode($qrcodeSvg),
                'data'=>$data
            ]);
        }else{
            $this->error('Client not found');
            return;
        }
        
        // Return the PDF for download
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, "certificate.pdf");
        }catch(\Exception $e){
            $this->error($e->getMessage());
            return;
        }
        }


    public function headers():array{
        return [
            ['key'=>'otherservice.name','label'=>'Other Service'],
            ['key'=>'period','label'=>'Period'],
            ['key'=>'status','label'=>'Status'],
            ['key'=>'actions','label'=>''],
        ];
    }
    public function render()
    {
        return view('livewire.components.otherapplications',['otherapplications'=>$this->getotherapplications(),'headers'=>$this->headers(),'otherservices'=>$this->getotherservices(),'sessions'=>$this->getsessions(),'professions'=>$this->professionlist()]);
    }
}
