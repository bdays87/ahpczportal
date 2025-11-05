<?php

namespace App\Livewire\Admin\Components;

use App\RenewalType;
use Livewire\Component;
use Mary\Traits\Toast;

class Renewalmodal extends Component
{
    use Toast;

    public $showModal = false;
    public $selectedTypes = [];
    public $typeOptions = [];

    protected $listeners = ['openRenewalModal'];

    public function mount()
    {
        $this->typeOptions = RenewalType::options();
    }

    public function openRenewalModal()
    {
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->selectedTypes = [];
    }

    public function toggleType($type)
    {
        if (in_array($type, $this->selectedTypes)) {
            $this->selectedTypes = array_filter($this->selectedTypes, fn($t) => $t !== $type);
        } else {
            $this->selectedTypes[] = $type;
        }
    }

    public function isSelected($type)
    {
        return in_array($type, $this->selectedTypes);
    }

    public function proceedWithRenewal()
    {
        if (empty($this->selectedTypes)) {
            $this->error('Please select at least one renewal type');
            return;
        }

        $selectedLabels = collect($this->selectedTypes)
            ->map(fn($type) => RenewalType::from($type)->label())
            ->join(', ');
        
        $this->success("Proceeding with: {$selectedLabels}");
        
        // Emit event to parent component with selected types
        $this->dispatch('renewalTypesSelected', $this->selectedTypes);
        
        $this->closeModal();
    }

    public function render()
    {
        return view('livewire.admin.components.renewalmodal');
    }
}
