<?php

namespace App\Livewire\Customer;

use App\Interfaces\iresourceInterface;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Resources extends Component
{
    public $search = '';

    protected $resourcerepo;

    public function boot(iresourceInterface $resourcerepo)
    {
        $this->resourcerepo = $resourcerepo;
    }

    public function getresources()
    {
        return $this->resourcerepo->getActiveResources($this->search);
    }

    public function download($id)
    {
        $resource = $this->resourcerepo->get($id);
        if ($resource && $resource->is_active && Storage::disk('public')->exists($resource->file_path)) {
            $filePath = Storage::disk('public')->path($resource->file_path);

            return response()->download($filePath, $resource->file_name);
        }

        return null;
    }

    #[Layout('components.layouts.app')]
    public function render()
    {
        return view('livewire.customer.resources', [
            'resources' => $this->getresources(),
        ]);
    }
}
