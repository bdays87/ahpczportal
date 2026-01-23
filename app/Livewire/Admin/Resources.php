<?php

namespace App\Livewire\Admin;

use App\Interfaces\iresourceInterface;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Mary\Traits\Toast;

class Resources extends Component
{
    use Toast, WithFileUploads;

    public $search = '';

    public $id;

    public $title;

    public $description;

    public $file;

    public $is_active = true;

    public $modal = false;

    public $modifymodal = false;

    protected $resourcerepo;

    public function boot(iresourceInterface $resourcerepo)
    {
        $this->resourcerepo = $resourcerepo;
    }

    public function getresources()
    {
        return $this->resourcerepo->getAll($this->search);
    }

    public function save()
    {
        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ];

        if (! $this->id) {
            $rules['file'] = 'required|file|max:10240'; // 10MB max
        } else {
            $rules['file'] = 'nullable|file|max:10240';
        }

        $this->validate($rules);

        $data = [
            'title' => $this->title,
            'description' => $this->description,
            'is_active' => $this->is_active,
        ];

        if ($this->file) {
            $path = $this->file->store('resources', 'public');
            $data['file_path'] = $path;
            $data['file_name'] = $this->file->getClientOriginalName();
            $data['file_size'] = $this->file->getSize();
            $data['file_type'] = $this->file->getMimeType();
        }

        if ($this->id) {
            $this->update();
        } else {
            $this->create($data);
        }
    }

    public function create($data)
    {
        $response = $this->resourcerepo->create($data);
        if ($response['status'] == 'success') {
            $this->success($response['message']);
            $this->reset(['title', 'description', 'file', 'is_active', 'id']);
            $this->modifymodal = false;
        } else {
            $this->error($response['message']);
        }
    }

    public function update()
    {
        $data = [
            'title' => $this->title,
            'description' => $this->description,
            'is_active' => $this->is_active,
        ];

        if ($this->file) {
            // Delete old file if exists
            $resource = $this->resourcerepo->get($this->id);
            if ($resource && $resource->file_path && Storage::disk('public')->exists($resource->file_path)) {
                Storage::disk('public')->delete($resource->file_path);
            }

            $path = $this->file->store('resources', 'public');
            $data['file_path'] = $path;
            $data['file_name'] = $this->file->getClientOriginalName();
            $data['file_size'] = $this->file->getSize();
            $data['file_type'] = $this->file->getMimeType();
        }

        $response = $this->resourcerepo->update($this->id, $data);
        if ($response['status'] == 'success') {
            $this->success($response['message']);
            $this->reset(['title', 'description', 'file', 'is_active', 'id']);
            $this->modifymodal = false;
        } else {
            $this->error($response['message']);
        }
    }

    public function delete($id)
    {
        $response = $this->resourcerepo->delete($id);
        if ($response['status'] == 'success') {
            $this->success($response['message']);
        } else {
            $this->error($response['message']);
        }
    }

    public function edit($id)
    {
        $resource = $this->resourcerepo->get($id);
        if ($resource) {
            $this->id = $id;
            $this->title = $resource->title;
            $this->description = $resource->description;
            $this->is_active = $resource->is_active;
            $this->modifymodal = true;
        }
    }

    public function download($id)
    {
        $resource = $this->resourcerepo->get($id);
        if ($resource && Storage::disk('public')->exists($resource->file_path)) {
            $filePath = Storage::disk('public')->path($resource->file_path);

            return response()->download($filePath, $resource->file_name);
        }
        $this->error('File not found.');

        return null;
    }

    public function headers(): array
    {
        return [
            ['key' => 'title', 'label' => 'Title'],
            ['key' => 'file_name', 'label' => 'File Name'],
            ['key' => 'is_active', 'label' => 'Status'],
            ['key' => 'created_at', 'label' => 'Created'],
            ['key' => 'actions', 'label' => ''],
        ];
    }

    #[Layout('components.layouts.app')]
    public function render()
    {
        return view('livewire.admin.resources', [
            'resources' => $this->getresources(),
            'headers' => $this->headers(),
        ]);
    }
}
