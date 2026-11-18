<?php

namespace App\Livewire\Admin\Components;

use App\Models\Customer;
use App\Models\Practitionermachine;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

/**
 * Machines used by practitioners.
 * - With a $customer it is scoped to that practitioner (profile / profession page).
 * - Without one it lists every machine (standalone page) and lets you pick the practitioner.
 */
class Practitionermachines extends Component
{
    use Toast, WithPagination;

    public $customer = null;

    public $customerprofession_id = null;

    public $search = '';

    public $statusfilter = '';

    public bool $modal = false;

    public $machine_id = null;

    // Practitioner picker (standalone mode only)
    public $machine_customer_id = null;

    public $machine_customer_name = null;

    public $customersearch = '';

    public $name;

    public $make;

    public $model;

    public $serial_number;

    public $asset_tag;

    public $location;

    public $date_from;

    public $date_to;

    public $status = 'IN_USE';

    public $notes;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusfilter()
    {
        $this->resetPage();
    }

    private function scoped()
    {
        return Practitionermachine::query()
            ->when($this->customer, fn ($q) => $q->where('customer_id', $this->customer->id));
    }

    public function openmodal($id = null)
    {
        $this->resetValidation();
        $this->reset('machine_id', 'name', 'make', 'model', 'serial_number', 'asset_tag', 'location', 'date_from', 'date_to', 'notes', 'customersearch', 'machine_customer_id', 'machine_customer_name');
        $this->status = 'IN_USE';

        if ($id) {
            $machine = $this->scoped()->with('customer')->find($id);
            if (! $machine) {
                $this->error('Machine record not found');

                return;
            }
            $this->machine_id = $machine->id;
            $this->machine_customer_id = $machine->customer_id;
            $this->machine_customer_name = trim(($machine->customer->name ?? '').' '.($machine->customer->surname ?? ''));
            $this->name = $machine->name;
            $this->make = $machine->make;
            $this->model = $machine->model;
            $this->serial_number = $machine->serial_number;
            $this->asset_tag = $machine->asset_tag;
            $this->location = $machine->location;
            $this->date_from = $machine->date_from?->format('Y-m-d');
            $this->date_to = $machine->date_to?->format('Y-m-d');
            $this->status = $machine->status;
            $this->notes = $machine->notes;
        }

        $this->modal = true;
    }

    public function pickcustomer($id)
    {
        $customer = Customer::find($id);
        if ($customer) {
            $this->machine_customer_id = $customer->id;
            $this->machine_customer_name = trim($customer->name.' '.$customer->surname).($customer->regnumber ? ' ('.$customer->regnumber.')' : '');
        }
        $this->customersearch = '';
    }

    public function clearcustomer()
    {
        $this->machine_customer_id = null;
        $this->machine_customer_name = null;
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'make' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'serial_number' => 'nullable|string|max:255',
            'asset_tag' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'status' => 'required|in:IN_USE,NOT_IN_USE',
        ]);

        $customerId = $this->customer ? $this->customer->id : $this->machine_customer_id;
        if (! $customerId) {
            $this->addError('machine_customer_id', 'Please select the practitioner.');

            return;
        }

        // An end date means the machine is no longer in use.
        $status = $this->date_to ? 'NOT_IN_USE' : $this->status;

        $data = [
            'name' => $this->name,
            'make' => $this->make,
            'model' => $this->model,
            'serial_number' => $this->serial_number,
            'asset_tag' => $this->asset_tag,
            'location' => $this->location,
            'date_from' => $this->date_from ?: null,
            'date_to' => $this->date_to ?: null,
            'status' => $status,
            'notes' => $this->notes,
        ];

        if ($this->machine_id) {
            $this->scoped()->where('id', $this->machine_id)->update($data + ['customer_id' => $customerId]);
        } else {
            Practitionermachine::create($data + [
                'uuid' => Str::uuid()->toString(),
                'customer_id' => $customerId,
                'customerprofession_id' => $this->customerprofession_id,
                'created_by' => Auth::id(),
            ]);
        }

        $this->success('Machine record saved');
        $this->modal = false;
    }

    /** Mark a machine as no longer in use (keeps the record and its history). */
    public function retire($id)
    {
        $this->scoped()->where('id', $id)
            ->update(['status' => 'NOT_IN_USE', 'date_to' => now()->format('Y-m-d')]);
        $this->success('Machine marked as not in use');
    }

    public function render()
    {
        $search = trim((string) $this->search);

        $machines = $this->scoped()
            ->with('customer')
            ->when($this->statusfilter, fn ($q) => $q->where('status', $this->statusfilter))
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($w) use ($search) {
                    $w->where('name', 'like', "%{$search}%")
                        ->orWhere('make', 'like', "%{$search}%")
                        ->orWhere('model', 'like', "%{$search}%")
                        ->orWhere('serial_number', 'like', "%{$search}%")
                        ->orWhere('asset_tag', 'like', "%{$search}%")
                        ->orWhereHas('customer', fn ($c) => $c->where('name', 'like', "%{$search}%")
                            ->orWhere('surname', 'like', "%{$search}%")
                            ->orWhere('regnumber', 'like', "%{$search}%"));
                });
            })
            ->orderByRaw("status = 'IN_USE' desc")
            ->orderByDesc('date_from')
            ->paginate(20);

        $customers = collect();
        if (! $this->customer && $this->modal && strlen(trim((string) $this->customersearch)) >= 2) {
            $s = trim($this->customersearch);
            $customers = Customer::where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                    ->orWhere('surname', 'like', "%{$s}%")
                    ->orWhere('regnumber', 'like', "%{$s}%");
            })->limit(10)->get();
        }

        return view('livewire.admin.components.practitionermachines', [
            'machines' => $machines,
            'customers' => $customers,
        ]);
    }
}
