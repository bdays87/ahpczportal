<?php

namespace App\Livewire\Admin\Components;

use App\Interfaces\icustomerprofessionInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Mary\Traits\Toast;

class Personaldetails extends Component
{
    use Toast;

    public $customer;

    public bool $namemodal = false;

    public $edit_name;

    public $edit_surname;

    public bool $regnumbermodal = false;

    public $regnumber_profession_id;

    public $regnumber_profession_name = '';

    public $edit_regnumber;

    /** Open the correction window for one profession's registration number. */
    public function openregnumber($customerprofession_id)
    {
        if (! Auth::user()->can('customers.modify')) {
            $this->error('You do not have permission to change registration numbers.');

            return;
        }
        $profession = $this->customer->customerprofessions()->with('profession')->find($customerprofession_id);
        if (! $profession) {
            $this->error('Profession not found for this customer.');

            return;
        }
        $this->resetValidation();
        $this->regnumber_profession_id = $profession->id;
        $this->regnumber_profession_name = $profession->profession->name ?? '';
        $this->edit_regnumber = $profession->registrationnumber;
        $this->regnumbermodal = true;
    }

    public function saveregnumber(icustomerprofessionInterface $professions)
    {
        if (! Auth::user()->can('customers.modify') || ! $this->customer->customerprofessions()->whereKey($this->regnumber_profession_id)->exists()) {
            $this->error('You cannot change this registration number.');

            return;
        }
        $this->validate(['edit_regnumber' => 'required|string|max:100'], ['edit_regnumber.required' => 'Please enter the registration number.']);

        $response = $professions->updateregnumber($this->regnumber_profession_id, $this->edit_regnumber);
        if ($response['status'] !== 'success') {
            $this->addError('edit_regnumber', $response['message']);

            return;
        }

        $this->customer->refresh();
        $this->regnumbermodal = false;
        $this->success($response['message']);
    }

    public function mount($customer)
    {
        $this->customer = $customer;
    }

    public function openname()
    {
        if (! Auth::user()->can('customers.modify')) {
            $this->error('You do not have permission to edit customers.');

            return;
        }
        $this->resetValidation();
        $this->edit_name = $this->customer->name;
        $this->edit_surname = $this->customer->surname;
        $this->namemodal = true;
    }

    /**
     * Correct a customer's name/surname directly (e.g. a spelling error),
     * without having to re-validate the rest of the profile. The previous
     * name field is left alone because this is a correction, not a name change.
     */
    public function savename()
    {
        if (! Auth::user()->can('customers.modify')) {
            $this->error('You do not have permission to edit customers.');

            return;
        }

        $this->validate([
            'edit_name' => 'required|string|max:255',
            'edit_surname' => 'required|string|max:255',
        ]);

        $name = trim($this->edit_name);
        $surname = trim($this->edit_surname);
        $old = $this->customer->name.' '.$this->customer->surname;

        $this->customer->forceFill(['name' => $name, 'surname' => $surname])->save();

        // Keep the login account's display name in step.
        $user = $this->customer->customeruser?->user;
        if ($user) {
            $user->forceFill(['name' => $name, 'surname' => $surname])->save();
        }

        Log::info('Customer name corrected', [
            'customer_id' => $this->customer->id,
            'from' => $old,
            'to' => $name.' '.$surname,
            'by' => Auth::id(),
        ]);

        $this->customer->refresh();
        $this->success('Name updated');
        $this->namemodal = false;
        $this->dispatch('$refresh');
    }

    public function render()
    {
        return view('livewire.admin.components.personaldetails');
    }
}
