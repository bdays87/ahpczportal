<?php

namespace App\Livewire\Components;

use App\Interfaces\iresourceInterface;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class Latestresources extends Component
{
    public $limit = 5;

    protected $resourceRepo;

    public function boot(iresourceInterface $resourceRepo)
    {
        $this->resourceRepo = $resourceRepo;
    }

    public function getLatestResources()
    {
        return $this->resourceRepo->getLatest($this->limit);
    }

    public function download($id)
    {
        $resource = $this->resourceRepo->get($id);
        if ($resource && $resource->is_active && Storage::disk('public')->exists($resource->file_path)) {
            $filePath = Storage::disk('public')->path($resource->file_path);

            return response()->download($filePath, $resource->file_name);
        }

        return null;
    }

    public function render()
    {
        return view('livewire.components.latestresources', [
            'resources' => $this->getLatestResources(),
        ]);
    }
}
