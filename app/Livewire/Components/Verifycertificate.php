<?php

namespace App\Livewire\Components;

use Livewire\Component;
use Mary\Traits\Toast;
use App\Models\Customerapplication;
class Verifycertificate extends Component
{
    use Toast;
  
    public $certificate_number;
    public $certificate;
    public function verifyCertificate()
    {
        $this->validate([
            'certificate_number' => 'required|string|max:255',
        ]);
        $searchresult = Customerapplication::with('customerprofession.customer.customeruser.user', 'customerprofession.profession', 'applicationtype','registertype')->where('certificate_number', $this->certificate_number)->first();
        if($searchresult){
            $this->certificate = $searchresult;
        }else{
             $this->error('Certificate not found');
             $this->certificate = null;
        }
    }
    public function render()
    {
        return view('livewire.components.verifycertificate');
    }
}
