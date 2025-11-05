<?php

namespace App\Livewire\Admin;

use App\Interfaces\imycdpInterface;
use Livewire\Component; 
use Mary\Traits\Toast;
use Illuminate\Support\Facades\Storage;

class Cpdapprovals extends Component
{

    use Toast;
    public $breadcrumbs = []; 
    protected $mycdprepo;
    public $year;
    public $status;
    public $cdp = null;

    public $points;
    public $comment;
    public bool $viewmodal = false;
    public $documenturl;
    public bool $viewattachmentmodal = false;
    public function mount(){
        $this->year = date('Y');
        $this->status = "AWAITING";
        $this->breadcrumbs = [
            [
                'label' => 'Dashboard',
                'icon' => 'o-home',
                'link' => route('dashboard'),
            ],
            [
                'label' => 'CPD Approvals'
            ],
        ];
    }
    public function boot(imycdpInterface $mycdprepo){
        $this->mycdprepo = $mycdprepo;
    }
    public function getcdps(){
        return $this->mycdprepo->getcdps($this->year,$this->status);
    }
    public function viewCdp($id){
        $this->cdp = $this->mycdprepo->get($id);
        $this->viewmodal = true;
    }
    public function viewattachment($id){
        $this->attachment = $this->cdp->attachments->where('id',$id)->first();
    
        $this->documenturl = Storage::url($this->attachment->file);
 
        $this->viewattachmentmodal = true;
    }

    public function  savepoints(){
        $this->validate([
            'points' => 'required',
            'comment' => 'required',
        ]);
        $response = $this->mycdprepo->assignpoints($this->cdp->id,['points'=>$this->points,'comment'=>$this->comment]);
        if($response['status']=='success'){
            $this->success($response['message']);
            $this->viewmodal = false;
        }else{
            $this->error($response['message']);
        }
       
    }
    public function render()
    {
        return view('livewire.admin.cpdapprovals',[
            'cdps'=>$this->getcdps(),
        ]);
    }
}
