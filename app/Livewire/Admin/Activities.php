<?php

namespace App\Livewire\Admin;

use App\Interfaces\iactivityInterface;
use App\Interfaces\iprofessionInterface;
use Livewire\Component;
use Livewire\WithFileUploads;
use Mary\Traits\Toast;

class Activities extends Component
{
    use Toast, WithFileUploads;

    public $breadcrumbs = [];

    protected $activityRepo;

    protected $professionRepo;

    // Modal states
    public $createModal = false;

    public $editModal = false;

    public $viewModal = false;

    public $selectedActivity = null;

    // Form properties
    public $title;

    public $description;

    public $type = 'ARTICLE';

    public $content_url;

    public $content_text;

    public $attachment_file;

    public $points = 10;

    public $duration_minutes = 60;

    public $status = 'DRAFT';

    public $profession_ids = [];

    public $activity_id;

    protected $rules = [
        'title' => 'required|string|max:255',
        'description' => 'required|string',
        'type' => 'required|in:VIDEO,ARTICLE,ATTACHMENT',
        'content_url' => 'nullable|url',
        'content_text' => 'nullable|string',
        'attachment_file' => 'nullable|file|max:10240', // 10MB max
        'points' => 'required|integer|min:1|max:100',
        'duration_minutes' => 'nullable|integer|min:1',
        'status' => 'required|in:DRAFT,PUBLISHED,ARCHIVED',
        'profession_ids' => 'required|array|min:1',
        'profession_ids.*' => 'exists:professions,id',
    ];

    public function mount()
    {
        $this->breadcrumbs = [
            ['label' => 'Dashboard', 'icon' => 'o-home', 'link' => route('dashboard')],
            ['label' => 'Activities Management'],
        ];
    }

    public function boot(iactivityInterface $activityRepo, iprofessionInterface $professionRepo)
    {
        $this->activityRepo = $activityRepo;
        $this->professionRepo = $professionRepo;
    }

    public function getActivities()
    {
        return $this->activityRepo->getAll();
    }

    public function getProfessions()
    {
        return $this->professionRepo->getAll(null, null)->map(function ($profession) {
            return ['id' => $profession->id, 'name' => $profession->name];
        })->toArray();
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->createModal = true;
    }

    public function openEditModal($id)
    {
        $activity = $this->activityRepo->get($id);
        if (! $activity) {
            $this->error('Activity not found');

            return;
        }

        $this->activity_id = $activity->id;
        $this->title = $activity->title;
        $this->description = $activity->description;
        $this->type = $activity->type;
        $this->content_url = $activity->content_url;
        $this->content_text = $activity->content_text;
        $this->points = $activity->points;
        $this->duration_minutes = $activity->duration_minutes;
        $this->status = $activity->status;
        $this->profession_ids = $activity->professions->pluck('id')->toArray();

        $this->editModal = true;
    }

    public function openViewModal($id)
    {
        $this->selectedActivity = $this->activityRepo->get($id);
        if (! $this->selectedActivity) {
            $this->error('Activity not found');

            return;
        }
        $this->viewModal = true;
    }

    public function save()
    {
        $this->validate();

        try {
            $data = [
                'title' => $this->title,
                'description' => $this->description,
                'type' => $this->type,
                'content_url' => $this->content_url,
                'content_text' => $this->content_text,
                'points' => $this->points,
                'duration_minutes' => $this->duration_minutes,
                'status' => $this->status,
                'profession_ids' => $this->profession_ids,
            ];

            // Handle file upload
            if ($this->attachment_file) {
                $path = $this->attachment_file->store('activities', 'public');
                $data['attachment_path'] = $path;
            }

            if ($this->activity_id) {
                $response = $this->activityRepo->update($this->activity_id, $data);
                $this->editModal = false;
            } else {
                $response = $this->activityRepo->create($data);
                $this->createModal = false;
            }

            if ($response['status'] === 'success') {
                $this->success($response['message']);
                $this->resetForm();
            } else {
                $this->error($response['message']);
            }
        } catch (\Exception $e) {
            $this->error('An error occurred: '.$e->getMessage());
        }
    }

    public function delete($id)
    {
        $response = $this->activityRepo->delete($id);

        if ($response['status'] === 'success') {
            $this->success($response['message']);
        } else {
            $this->error($response['message']);
        }
    }

    public function publish($id)
    {
        $response = $this->activityRepo->publish($id);

        if ($response['status'] === 'success') {
            $this->success($response['message']);
        } else {
            $this->error($response['message']);
        }
    }

    public function closeModals()
    {
        $this->createModal = false;
        $this->editModal = false;
        $this->viewModal = false;
        $this->selectedActivity = null;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->reset([
            'title', 'description', 'type', 'content_url', 'content_text',
            'attachment_file', 'points', 'duration_minutes', 'status',
            'profession_ids', 'activity_id',
        ]);
        $this->type = 'ARTICLE';
        $this->points = 10;
        $this->duration_minutes = 60;
        $this->status = 'DRAFT';
    }

    public function render()
    {
        return view('livewire.admin.activities', [
            'activities' => $this->getActivities(),
            'professions' => $this->getProfessions(),
        ]);
    }
}
