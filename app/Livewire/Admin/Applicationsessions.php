<?php

namespace App\Livewire\Admin;

use App\Interfaces\iapplicationsessionInterface;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Mary\Traits\Toast;

class Applicationsessions extends Component
{
    use Toast;

    public $year;
    public $renewal_window_start_date;
    public $renewal_window_end_date;

    public $breadcrumbs = [];

    public $id;

    public $modal = false;
    protected  $repo;
    public function boot(iapplicationsessionInterface $repo)
    {
        $this->repo = $repo;
    }
    public function mount()
    {
        $this->breadcrumbs = [
            [
                'label' => 'Dashboard',
                'icon' => 'o-home',
                'link' => route('dashboard'),
            ],
            [
                'label' => 'Application Sessions'
            ],
        ];
    }

    public function getapplicationsessions()
    {
        return $this->repo->getAll();
    }

    public function save()
    {
        $this->validate([
            'year' => 'required|integer|min:2020|max:2100',
            'renewal_window_start_date' => 'required|date',
            'renewal_window_end_date' => 'required|date|after_or_equal:renewal_window_start_date',
        ]);
        if($this->id){
            $this->update();
        }else{
            $this->create();
        }

        $this->reset(['year', 'renewal_window_start_date', 'renewal_window_end_date', 'id']);
    }

    public function edit($id)
    {
        $applicationsession = $this->repo->get($id);

        if (!$applicationsession) {
            $this->error('Application session not found.');
            return;
        }
        $this->year = $applicationsession->year;
        $this->renewal_window_start_date = $applicationsession->renewal_window_start_date?->format('Y-m-d');
        $this->renewal_window_end_date = $applicationsession->renewal_window_end_date?->format('Y-m-d');
        $this->id = $id;
        $this->modal = true;
    }

    public function create(){
        $response = $this->repo->create([
            'year' => $this->year,
            'user_id' => Auth::user()->id,
            'renewal_window_start_date' => $this->renewal_window_start_date,
            'renewal_window_end_date' => $this->renewal_window_end_date,
        ]);
        if ($response["status"] == "success") {
            $this->success($response["message"]);
        } else{
            $this->error($response["message"]);
        }
    }

    public function update()
    {
        $response = $this->repo->update($this->id,[
            'year' => $this->year,
            'user_id' => Auth::user()->id,
            'renewal_window_start_date' => $this->renewal_window_start_date,
            'renewal_window_end_date' => $this->renewal_window_end_date,
        ]);
        if ($response["status"] == "success") {
            $this->success($response["message"]);
        } else{
            $this->error($response["message"]);
        }
    }

    public function toggleactive($id)
    {
        $response = $this->repo->toggleactive($id);
        if ($response['status'] == "success") {
            $this->success($response['message']);
        } else{
            $this->error($response['message']);
        }
    }

    public function delete($id)
    {
        $response = $this->repo->delete($id);
        if ($response['status'] == "success") {
            $this->success($response['message']);
        } else{
            $this->error($response['message']);
        }
    }

    public function headers():array{
        return [
            ['key' => 'year', 'label' => 'Year', 'sortable' => true],
            ['key' => 'renewal_window_label', 'label' => 'Renewal Window'],
            ['key' => 'is_active', 'label' => 'Status'],
            ['key' => 'user', 'label' => 'User', 'sortable' => false]
        ];
    }

    public function render()
    {
        return view('livewire.admin.applicationsessions',[
            'applicationsessions' => $this->getapplicationsessions(),
            'headers' => $this->headers(),
        ]);
    }
}
