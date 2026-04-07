<?php

namespace App\Livewire\Auth;

use App\Interfaces\iaccounttypeInterface;
use App\Interfaces\iuserInterface;
use App\Models\Customer;
use App\Models\Customeruser;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Mary\Traits\Toast;

class Register extends Component
{
    use Toast;

    // Step 1 — search
    public $step = 1;
    public $searchquery = '';
    public $foundcustomer = null;
    public $searcherror = '';

    // Step 2a — existing customer linking
    public $email = '';
    public $phone = '';
    public $password = '';
    public $password_confirmation = '';
    public $accounttype_id = '';

    // Step 2b — new customer full form
    public $name = '';
    public $surname = '';

    protected $accounttyperepo;
    protected $userrepo;

    public function boot(iaccounttypeInterface $accounttyperepo, iuserInterface $userrepo)
    {
        $this->accounttyperepo = $accounttyperepo;
        $this->userrepo        = $userrepo;
    }

    public function getaccounttypes()
    {
        return $this->accounttyperepo->getAll(null)->where('id', '!=', 1);
    }

    // ── Step 1: Search ────────────────────────────────────────────────────────

    public function search()
    {
        $this->validate(['searchquery' => 'required|string|min:3']);

        $this->searcherror  = '';
        $this->foundcustomer = null;

        $customer = Customer::where('identificationnumber', $this->searchquery)
            ->orWhere('regnumber', $this->searchquery)
            ->first();

        if ($customer) {
            // Check if already has a login
            if ($customer->customeruser) {
                $this->searcherror = 'This record already has a login account. Please use the Sign In page.';
                return;
            }
            $this->foundcustomer = $customer;
            $this->name          = $customer->name;
            $this->surname       = $customer->surname;
            $this->step          = 2; // existing customer — just needs credentials
        } else {
            $this->step = 3; // not found — full form
        }
    }

    public function backtosearch()
    {
        $this->step          = 1;
        $this->foundcustomer = null;
        $this->searcherror   = '';
        $this->reset(['email', 'phone', 'password', 'password_confirmation', 'accounttype_id', 'name', 'surname']);
    }

    // ── Step 2: Link existing customer ───────────────────────────────────────

    public function linkaccount()
    {
        $this->validate([
            'email'                 => 'required|email|unique:users,email',
            'phone'                 => ['required', 'regex:/^(?:\+263|0)7[1378]\d{7}$/'],
            'password'              => 'required|min:8|confirmed',
            'password_confirmation' => 'required',
            'accounttype_id'        => 'required|exists:accounttypes,id',
        ]);

        $response = $this->userrepo->register([
            'name'           => $this->foundcustomer->name,
            'surname'        => $this->foundcustomer->surname,
            'email'          => $this->email,
            'phone'          => $this->phone,
            'password'       => $this->password,
            'accounttype_id' => $this->accounttype_id,
        ]);

        if ($response['status'] !== 'success') {
            $this->error($response['message']);
            return;
        }

        // Link user to existing customer
        Customeruser::create([
            'customer_id' => $this->foundcustomer->id,
            'user_id'     => $response['data']->id,
        ]);

        $this->success('Account created and linked successfully.');
        $this->redirect(route('login'));
    }

    // ── Step 3: Full new registration ────────────────────────────────────────

    public function register()
    {
        $this->validate([
            'name'                  => 'required|string|max:255',
            'surname'               => 'required|string|max:255',
            'email'                 => 'required|email|unique:users,email',
            'phone'                 => ['required', 'regex:/^(?:\+263|0)7[1378]\d{7}$/'],
            'password'              => 'required|min:8|confirmed',
            'password_confirmation' => 'required',
            'accounttype_id'        => 'required|exists:accounttypes,id',
        ]);

        $response = $this->userrepo->register([
            'name'           => $this->name,
            'surname'        => $this->surname,
            'email'          => $this->email,
            'phone'          => $this->phone,
            'password'       => $this->password,
            'accounttype_id' => $this->accounttype_id,
        ]);

        if ($response['status'] === 'success') {
            $this->success('Account created successfully.');
            $this->redirect(route('login'));
        } else {
            $this->error($response['message']);
        }
    }

    #[Layout('components.layouts.plain')]
    public function render()
    {
        return view('livewire.auth.register', [
            'accounttypes' => $this->getaccounttypes(),
        ]);
    }
}
