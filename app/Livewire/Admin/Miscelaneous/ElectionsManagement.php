<?php

namespace App\Livewire\Admin\Miscelaneous;

use App\Interfaces\ielectionInterface;
use App\Models\City;
use App\Models\Province;
use Livewire\Component;
use Livewire\WithFileUploads;
use Mary\Traits\Toast;

class ElectionsManagement extends Component
{
    use Toast, WithFileUploads;

    protected $electionRepo;

    public $breadcrumbs = [];

    // Election Properties
    public $selectedElection = null;

    public $election_name;

    public $election_description;

    public $election_start_date;

    public $election_end_date;

    public $election_status = 'DRAFT';

    public $election_year;

    public $election_publish_status = 'DRAFT';

    // Position Properties
    public $selectedPosition = null;

    public $position_name;

    public $position_description;

    // Candidate Properties
    public $selectedCandidate = null;

    public $candidate_customer_id;

    public $candidate_description;

    public $candidate_profile_picture;

    // Modals
    public $createElectionModal = false;

    public $editElectionModal = false;

    public $addPositionModal = false;

    public $addCandidateModal = false;

    public $viewResultsModal = false;

    // Filters
    public $province_id;

    public $city_id;

    public $search;

    public function boot(ielectionInterface $electionRepo): void
    {
        $this->electionRepo = $electionRepo;
    }

    public function mount(): void
    {
        $this->breadcrumbs = [
            ['label' => 'Dashboard', 'icon' => 'o-home', 'link' => route('dashboard')],
            ['label' => 'Elections Management'],
        ];
        $this->election_year = date('Y');
    }

    public function getElectionsProperty()
    {
        return $this->electionRepo->getAll();
    }

    public function getCompliantPractitionersProperty()
    {
        return $this->electionRepo->getCompliantPractitioners([
            'province_id' => $this->province_id,
            'city_id' => $this->city_id,
            'search' => $this->search,
        ]);
    }

    public function getProvincesProperty()
    {
        return Province::orderBy('name')->get();
    }

    public function getCitiesProperty()
    {
        if ($this->province_id) {
            return City::where('province_id', $this->province_id)->orderBy('name')->get();
        }

        return City::orderBy('name')->get();
    }

    public function updatedProvinceId(): void
    {
        $this->city_id = null;
    }

    // Election CRUD
    public function createElection(): void
    {
        $this->validate([
            'election_name' => 'required|string|max:255',
            'election_description' => 'nullable|string',
            'election_start_date' => 'required|date|after:now',
            'election_end_date' => 'required|date|after:election_start_date',
            'election_year' => 'required|integer|min:2020|max:2100',
        ]);

        $this->electionRepo->create([
            'name' => $this->election_name,
            'description' => $this->election_description,
            'start_date' => $this->election_start_date,
            'end_date' => $this->election_end_date,
            'year' => $this->election_year,
            'publish_status' => 'DRAFT',
        ]);

        $this->success('Election created successfully');
        $this->resetElectionForm();
        $this->createElectionModal = false;
    }

    public function editElection($id): void
    {
        $election = $this->electionRepo->getById($id);
        $this->selectedElection = $election;
        $this->election_name = $election->name;
        $this->election_description = $election->description;
        $this->election_start_date = $election->start_date->format('Y-m-d\TH:i');
        $this->election_end_date = $election->end_date->format('Y-m-d\TH:i');
        $this->election_status = $election->status;
        $this->election_year = $election->year;
        $this->election_publish_status = $election->publish_status;
        $this->editElectionModal = true;
    }

    public function updateElection(): void
    {
        $this->validate([
            'election_name' => 'required|string|max:255',
            'election_description' => 'nullable|string',
            'election_start_date' => 'required|date',
            'election_end_date' => 'required|date|after:election_start_date',
            'election_year' => 'required|integer|min:2020|max:2100',
        ]);

        $this->electionRepo->update($this->selectedElection->id, [
            'name' => $this->election_name,
            'description' => $this->election_description,
            'start_date' => $this->election_start_date,
            'end_date' => $this->election_end_date,
            'status' => $this->election_status,
            'year' => $this->election_year,
            'publish_status' => $this->election_publish_status,
        ]);

        $this->success('Election updated successfully');
        $this->editElectionModal = false;
        $this->resetElectionForm();
    }

    public function publishElection($id): void
    {
        $election = $this->electionRepo->getById($id);

        if ($election->positions()->count() === 0) {
            $this->error('Cannot publish election without positions');

            return;
        }

        $this->electionRepo->update($id, ['publish_status' => 'PUBLISHED']);
        $this->success('Election published successfully');
    }

    public function startElection($id): void
    {
        $election = $this->electionRepo->getById($id);

        if ($election->publish_status !== 'PUBLISHED') {
            $this->error('Election must be published before starting');

            return;
        }

        if ($election->positions()->count() === 0) {
            $this->error('Cannot start election without positions');

            return;
        }

        $this->electionRepo->update($id, ['status' => 'RUNNING']);
        $this->success('Election started successfully');
    }

    public function closeElection($id): void
    {
        $this->electionRepo->update($id, ['status' => 'CLOSED']);
        $this->success('Election closed successfully');
    }

    // Position Management
    public function openAddPositionModal($electionId): void
    {
        $this->selectedElection = $this->electionRepo->getById($electionId);
        $this->addPositionModal = true;
    }

    public function addPosition(): void
    {
        $this->validate([
            'position_name' => 'required|string|max:255',
            'position_description' => 'nullable|string',
        ]);

        $this->electionRepo->addPosition($this->selectedElection->id, [
            'name' => $this->position_name,
            'description' => $this->position_description,
        ]);

        $this->success('Position added successfully');
        $this->resetPositionForm();
        $this->addPositionModal = false;
    }

    // Candidate Management
    public function openAddCandidateModal($positionId): void
    {
        $election = $this->electionRepo->getAll()->firstWhere(function ($election) use ($positionId) {
            return $election->positions->contains('id', $positionId);
        });

        $this->selectedPosition = $election->positions->firstWhere('id', $positionId);
        $this->addCandidateModal = true;
    }

    public function addCandidate(): void
    {
        $this->validate([
            'candidate_customer_id' => 'required|exists:customers,id',
            'candidate_description' => 'required|string|max:1000',
            'candidate_profile_picture' => 'nullable|image|max:2048',
        ]);

        $profilePicturePath = null;
        if ($this->candidate_profile_picture) {
            $profilePicturePath = $this->candidate_profile_picture->store('election-candidates', 'public');
        }

        $result = $this->electionRepo->addCandidate($this->selectedPosition->id, [
            'customer_id' => $this->candidate_customer_id,
            'description' => $this->candidate_description,
            'profile_picture' => $profilePicturePath,
        ]);

        if ($result['status'] === 'error') {
            $this->error($result['message']);

            return;
        }

        $this->success($result['message']);
        $this->resetCandidateForm();
        $this->addCandidateModal = false;
    }

    public function removeCandidate($candidateId): void
    {
        $result = $this->electionRepo->removeCandidate($candidateId);
        $this->success($result['message']);
    }

    // Results
    public function viewResults($electionId): void
    {
        $this->selectedElection = $this->electionRepo->getById($electionId);
        $this->viewResultsModal = true;
    }

    private function resetElectionForm(): void
    {
        $this->selectedElection = null;
        $this->election_name = null;
        $this->election_description = null;
        $this->election_start_date = null;
        $this->election_end_date = null;
        $this->election_status = 'DRAFT';
        $this->election_year = date('Y');
        $this->election_publish_status = 'DRAFT';
    }

    private function resetPositionForm(): void
    {
        $this->selectedPosition = null;
        $this->position_name = null;
        $this->position_description = null;
    }

    private function resetCandidateForm(): void
    {
        $this->selectedCandidate = null;
        $this->candidate_customer_id = null;
        $this->candidate_description = null;
        $this->candidate_profile_picture = null;
    }

    public function render()
    {
        return view('livewire.admin.miscelaneous.elections-management', [
            'elections' => $this->elections,
            'compliantPractitioners' => $this->compliantPractitioners,
            'provinces' => $this->provinces,
            'cities' => $this->cities,
        ]);
    }
}
