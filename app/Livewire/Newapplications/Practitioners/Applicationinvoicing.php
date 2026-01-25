<?php

namespace App\Livewire\Newapplications\Practitioners;

use App\Interfaces\icurrencyInterface;
use App\Interfaces\icustomerprofessionInterface;
use App\Interfaces\invoiceInterface;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;
use Mary\Traits\Toast;

class Applicationinvoicing extends Component
{
    use Toast,WithFileUploads;

    public $uuid;

    public $breadcrumbs = [];

    public $customerprofession_id;

    public $step = 5;

    public $applicationtype_id;

    protected $customerprofessionrepo;

    protected $invoicerepo;

    protected $currencyrepo;

    public function mount($uuid)
    {
        $this->uuid = $uuid;

        if (Auth::user()->accounttype_id == 1) {
            $this->breadcrumbs = [
                [
                    'label' => 'Dashboard',
                    'icon' => 'o-home',
                    'link' => route('dashboard'),
                ],
                [
                    'label' => 'Customer',
                    'icon' => 'o-home',
                    'link' => route('customers.index'),
                ],
                [
                    'label' => 'Customer Professions',
                ],
            ];

        } else {
            $this->breadcrumbs = [
                [
                    'label' => 'Dashboard',
                    'icon' => 'o-home',
                    'link' => route('dashboard'),
                ],
                [
                    'label' => 'My Profession',
                ],
            ];
        }

    }

    public function boot(icustomerprofessionInterface $customerprofessionrepo, invoiceInterface $invoicerepo, icurrencyInterface $currencyrepo)
    {
        $this->customerprofessionrepo = $customerprofessionrepo;
        $this->invoicerepo = $invoicerepo;
        $this->currencyrepo = $currencyrepo;
    }

    public function getcurrencies()
    {
        return $this->currencyrepo->getAll('active');
    }

    public function getcustomerprofession()
    {
        $payload = $this->customerprofessionrepo->getbyuuid($this->uuid);
        if ($payload['customerprofession']->applications->count() > 0) {
            $this->applicationtype_id = $payload['customerprofession']->applications->last()->applicationtype_id;
        } else {
            $this->applicationtype_id = 1;
        }

        $this->customerprofession_id = $payload['customerprofession']['id'];

        return $payload;
    }

    #[On('invoicesettled')]
    public function getinvoice()
    {
        $type = 'New Application';
        if ($this->applicationtype_id != 1) {
            $type = 'Renewal';
        }

        $invoices = $this->invoicerepo->getcustomerprofessioninvoices($this->customerprofession_id, $type);

        if (count($invoices) > 0) {
            $invoice = collect($invoices['data'])->last();

            return $invoice;
        }

        return null;
    }

    public function render()
    {
        return view('livewire.newapplications.practitioners.applicationinvoicing', [
            'customerprofession' => $this->getcustomerprofession()['customerprofession'],
            'invoice' => $this->getinvoice(),
            'currencies' => $this->getcurrencies(),
        ]);
    }
}
