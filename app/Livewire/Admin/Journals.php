<?php

namespace App\Livewire\Admin;

use App\Interfaces\ijournalInterface;
use Livewire\Component;
use Mary\Traits\Toast;

class Journals extends Component
{
    use Toast;

    public $breadcrumbs = [];

    protected $journalRepo;

    // Modal states
    public $createModal = false;

    public $editModal = false;

    public $viewModal = false;

    public $selectedJournal = null;

    // Form properties
    public $title;

    public $author;

    public $published_date;

    public $link;

    public $journal_id;

    public $search = '';

    protected $rules = [
        'title' => 'required|string|max:255',
        'author' => 'nullable|string|max:255',
        'published_date' => 'nullable|date',
        'link' => 'required|url|max:500',
    ];

    public function mount()
    {
        $this->breadcrumbs = [
            ['label' => 'Dashboard', 'icon' => 'o-home', 'link' => route('dashboard')],
            ['label' => 'Journals Management'],
        ];
    }

    public function boot(ijournalInterface $journalRepo)
    {
        $this->journalRepo = $journalRepo;
    }

    public function getJournals()
    {
        return $this->journalRepo->getAll($this->search);
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->createModal = true;
    }

    public function openEditModal($id)
    {
        $journal = $this->journalRepo->get($id);
        if (! $journal) {
            $this->error('Journal not found');

            return;
        }

        $this->journal_id = $journal->id;
        $this->title = $journal->title;
        $this->author = $journal->author;
        $this->published_date = $journal->published_date?->format('Y-m-d');
        $this->link = $journal->link;

        $this->editModal = true;
    }

    public function openViewModal($id)
    {
        $this->selectedJournal = $this->journalRepo->get($id);
        if (! $this->selectedJournal) {
            $this->error('Journal not found');

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
                'author' => $this->author,
                'published_date' => $this->published_date,
                'link' => $this->link,
            ];

            if ($this->journal_id) {
                $response = $this->journalRepo->update($this->journal_id, $data);
                $this->editModal = false;
            } else {
                $response = $this->journalRepo->create($data);
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
        $response = $this->journalRepo->delete($id);

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
        $this->selectedJournal = null;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->reset([
            'title', 'author', 'published_date', 'link', 'journal_id',
        ]);
    }

    public function render()
    {
        return view('livewire.admin.journals', [
            'journals' => $this->getJournals(),
        ]);
    }
}
