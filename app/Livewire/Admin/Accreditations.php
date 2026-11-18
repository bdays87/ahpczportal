<?php

namespace App\Livewire\Admin;

use App\Models\Accreditation;
use App\Models\Accreditationlevel;
use App\Models\Otherapplicationinstaccreditation;
use Livewire\Component;
use Mary\Traits\Toast;

class Accreditations extends Component
{
    use Toast;

    public $breadcrumbs = [];

    public $search = '';

    public bool $modal = false;

    public $accreditation_id = null;

    public $name;

    public $description;

    public bool $levelmodal = false;

    public $level_accreditation_id = null;

    public $level_accreditation_name = '';

    public $levelname;

    public function mount()
    {
        $this->breadcrumbs = [
            ['label' => 'Dashboard', 'icon' => 'o-home', 'link' => route('dashboard')],
            ['label' => 'Accreditations'],
        ];
    }

    public function openmodal($id = null)
    {
        $this->resetValidation();
        $this->reset('accreditation_id', 'name', 'description');
        if ($id && $a = Accreditation::find($id)) {
            $this->accreditation_id = $a->id;
            $this->name = $a->name;
            $this->description = $a->description;
        }
        $this->modal = true;
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255|unique:accreditations,name,'.($this->accreditation_id ?: 'NULL').',id',
            'description' => 'nullable|string|max:255',
        ]);

        if ($this->accreditation_id) {
            $a = Accreditation::findOrFail($this->accreditation_id);
            $old = $a->name;
            $a->update(['name' => trim($this->name), 'description' => $this->description]);
            // Keep the text copy on institutions in step with a rename.
            if ($old !== $a->name) {
                Otherapplicationinstaccreditation::where('accreditation_id', $a->id)->update(['name' => $a->name]);
            }
        } else {
            Accreditation::create(['name' => trim($this->name), 'description' => $this->description, 'status' => 'ACTIVE']);
        }

        $this->success('Accreditation saved');
        $this->modal = false;
    }

    public function delete($id)
    {
        if (Otherapplicationinstaccreditation::where('accreditation_id', $id)->exists()) {
            $this->error('This accreditation is held by institutions and cannot be deleted.');

            return;
        }
        Accreditationlevel::where('accreditation_id', $id)->delete();
        Accreditation::where('id', $id)->delete();
        $this->success('Accreditation deleted');
    }

    public function openlevelmodal($accreditationId)
    {
        $a = Accreditation::find($accreditationId);
        if (! $a) {
            return;
        }
        $this->resetValidation();
        $this->level_accreditation_id = $a->id;
        $this->level_accreditation_name = $a->name;
        $this->levelname = '';
        $this->levelmodal = true;
    }

    public function savelevel()
    {
        $this->validate(['levelname' => 'required|string|max:255']);

        $name = trim($this->levelname);
        if (Accreditationlevel::where('accreditation_id', $this->level_accreditation_id)->where('name', $name)->exists()) {
            $this->addError('levelname', 'This level already exists.');

            return;
        }

        $next = (int) Accreditationlevel::where('accreditation_id', $this->level_accreditation_id)->max('sort_order') + 1;
        Accreditationlevel::create([
            'accreditation_id' => $this->level_accreditation_id,
            'name' => $name,
            'sort_order' => $next,
            'status' => 'ACTIVE',
        ]);
        $this->levelname = '';
        $this->success('Level added');
    }

    public function deletelevel($id)
    {
        if (Otherapplicationinstaccreditation::where('accreditationlevel_id', $id)->exists()) {
            $this->error('This level is in use by institutions and cannot be deleted.');

            return;
        }
        Accreditationlevel::where('id', $id)->delete();
        $this->success('Level deleted');
    }

    public function render()
    {
        $search = trim((string) $this->search);

        return view('livewire.admin.accreditations', [
            'accreditations' => Accreditation::with('levels')
                ->when($search !== '', fn ($q) => $q->where('name', 'like', "%{$search}%"))
                ->orderBy('name')
                ->get(),
            'levelsfor' => $this->level_accreditation_id
                ? Accreditationlevel::where('accreditation_id', $this->level_accreditation_id)->orderBy('sort_order')->orderBy('id')->get()
                : collect(),
        ]);
    }
}
