<?php

namespace App\Livewire\Admin;

use App\Interfaces\inewsletterInterface;
use Livewire\Component;
use Mary\Traits\Toast;

class Newsletters extends Component
{
    use Toast;

    public $breadcrumbs = [];

    protected $newsletterRepo;

    // Modal states
    public $createModal = false;

    public $editModal = false;

    public $viewModal = false;

    public $selectedNewsletter = null;

    // Form properties
    public $title;

    public $link;

    public $published_date;

    public $newsletter_id;

    public $search = '';

    public $selectedYear;

    protected $rules = [
        'title' => 'required|string|max:255',
        'link' => 'required|url|max:500',
        'published_date' => 'nullable|date',
    ];

    public function mount()
    {
        $this->breadcrumbs = [
            ['label' => 'Dashboard', 'icon' => 'o-home', 'link' => route('dashboard')],
            ['label' => 'Newsletters Management'],
        ];
        $this->selectedYear = date('Y');
    }

    public function boot(inewsletterInterface $newsletterRepo)
    {
        $this->newsletterRepo = $newsletterRepo;
    }

    public function getNewsletters()
    {
        return $this->newsletterRepo->getAll($this->selectedYear);
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->createModal = true;
    }

    public function openEditModal($id)
    {
        $newsletter = $this->newsletterRepo->get($id);
        if (! $newsletter) {
            $this->error('Newsletter not found');

            return;
        }

        $this->newsletter_id = $newsletter->id;
        $this->title = $newsletter->title;
        $this->link = $newsletter->link;
        $this->published_date = $newsletter->published_date?->format('Y-m-d');

        $this->editModal = true;
    }

    public function openViewModal($id)
    {
        $this->selectedNewsletter = $this->newsletterRepo->get($id);
        if (! $this->selectedNewsletter) {
            $this->error('Newsletter not found');

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
                'link' => $this->link,
                'published_date' => $this->published_date,
            ];

            if ($this->newsletter_id) {
                $response = $this->newsletterRepo->update($this->newsletter_id, $data);
                $this->editModal = false;
            } else {
                $response = $this->newsletterRepo->create($data);
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
        $response = $this->newsletterRepo->delete($id);

        if ($response['status'] === 'success') {
            $this->success($response['message']);
        } else {
            $this->error($response['message']);
        }
    }

    public function broadcast($id)
    {
        $response = $this->newsletterRepo->broadcast($id);

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
        $this->selectedNewsletter = null;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->reset([
            'title', 'link', 'published_date', 'newsletter_id',
        ]);
    }

    public function render()
    {
        return view('livewire.admin.newsletters', [
            'newsletters' => $this->getNewsletters(),
        ]);
    }
}
