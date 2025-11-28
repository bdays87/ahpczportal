<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Province;
use App\Models\City;
use App\Models\Profession;
use App\Models\Registertype;
use Livewire\WithPagination;
use App\Interfaces\icustomerapplicationInterface;
class Practitionerlist extends Component
{
    use WithPagination;
    public $province_id = null;
    public $city_id = null;
    public $profession_id = null;
    public $registertype_id = null;
    public $gender = null;
    public $search = null;
    public $date_from = null;
    public $date_to = null;
    public $year = null;

    protected $applicationrepo;
    public function boot(icustomerapplicationInterface $applicationrepo)
    {
        $this->applicationrepo = $applicationrepo;
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

    public function getProfessionsProperty()
    {
        return Profession::orderBy('name')->get();
    }

    public function getRegistertypesProperty()
    {
        return Registertype::orderBy('name')->get();
    }
    
  

    public function getGenderOptionsProperty()
    {
        return [
            ['id' => 'MALE', 'name' => 'Male'],
            ['id' => 'FEMALE', 'name' => 'Female'],
            ['id' => 'OTHER', 'name' => 'Other'],
        ];
    }

    public function headers():array{
        return [
            ['key' => 'gender', 'label' => 'Gender'],
            ['key' => 'regnumber', 'label' => 'Regnumber'],
            ['key' => 'name', 'label' => 'Name'],
            ['key' => 'surname', 'label' => 'Surname'],
            ['key' => 'profession', 'label' => 'Profession'],
            ['key' => 'province', 'label' => 'Province'],
            ['key' => 'city', 'label' => 'City'],
            ['key' => 'status', 'label' => 'Status']
        ];
    }

    public function getApplicationsProperty()
    {
        return $this->applicationrepo->compliancereportData([
            'province_id' => $this->province_id,
            'city_id' => $this->city_id,
            'profession_id' => $this->profession_id,
            'registertype_id' => $this->registertype_id,
            'gender' => $this->gender,
            'search' => $this->search,
            'year' => $this->year,
        ]);
    }

     #[Layout('components.layouts.plain')]
    public function render()
    {
        return view('livewire.practitionerlist',[
           
            'provinces' => $this->provinces,
            'cities' => $this->cities,
            'professions' => $this->professions,
            'genderOptions' => $this->genderOptions,
            'headers' => $this->headers(),
            'applications' => $this->applications,
        ]);
    }
}
