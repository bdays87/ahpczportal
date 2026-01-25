<?php

namespace App\Livewire\Admin;

use App\Interfaces\icustomerhistoricaldataInterface;
use App\Interfaces\inationalityInterface;
use App\Interfaces\iprofessionInterface;
use App\Interfaces\iregistertypeInterface;
use App\Models\Customerhistoricaldata;
use App\Models\ProfessionTire;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;
use Mary\Traits\Toast;

class Createhistoricaldata extends Component
{
    use Toast, WithFileUploads;

    public $breadcrumbs = [];

    protected $repo;

    protected $nationalityrepo;

    protected $professionrepo;

    protected $registertyperepo;

    public $mode = 'single'; // 'single' or 'import'

    public $file;

    // Single record form fields
    public $name;

    public $surname;

    public $email;

    public $phone;

    public $gender;

    public $identificationnumber;

    public $dob;

    public $identificationtype = 'NATIONAL_ID';

    public $nationality_id;

    public $address;

    public $placeofbirth;

    public $accounttype_id = 2;

    public $professions = [];

    public $createmodal = false;

    public $importmodal = false;

    public function mount()
    {
        $this->breadcrumbs = [
            [
                'label' => 'Dashboard',
                'icon' => 'o-home',
                'link' => route('dashboard'),
            ],
            [
                'label' => 'Create Historical Data',
            ],
        ];
        $this->addProfession();
    }

    public function boot(
        icustomerhistoricaldataInterface $repo,
        inationalityInterface $nationalityrepo,
        iprofessionInterface $professionrepo,
        iregistertypeInterface $registertyperepo
    ) {
        $this->repo = $repo;
        $this->nationalityrepo = $nationalityrepo;
        $this->professionrepo = $professionrepo;
        $this->registertyperepo = $registertyperepo;
    }

    public function getNationalities()
    {
        return $this->nationalityrepo->getAll(null);
    }

    public function getProfessions()
    {
        return $this->professionrepo->getAll(null, null);
    }

    public function getRegistertypes()
    {
        return $this->registertyperepo->getAll();
    }

    public function getTiresForProfession($professionId)
    {
        if (! $professionId) {
            return [];
        }

        return ProfessionTire::where('profession_id', $professionId)
            ->with('tire')
            ->get()
            ->pluck('tire')
            ->filter()
            ->values();
    }

    public function addProfession()
    {
        $this->professions[] = [
            'profession_id' => null,
            'tire_id' => null,
            'registrationnumber' => '',
            'registrationyear' => '',
            'practisingcertificatenumber' => '',
            'registertype_id' => null,
            'last_renewal_year' => '',
            'last_renewal_year_cdp_points' => '',
            'last_renewal_expire_date' => '',
            'certificates' => [],
            'descriptions' => [],
        ];
    }

    public function addCertificate($professionIndex)
    {
        if (! isset($this->professions[$professionIndex]['certificates'])) {
            $this->professions[$professionIndex]['certificates'] = [];
            $this->professions[$professionIndex]['descriptions'] = [];
        }
        $this->professions[$professionIndex]['certificates'][] = null;
        $this->professions[$professionIndex]['descriptions'][] = '';
    }

    public function removeCertificate($professionIndex, $certificateIndex)
    {
        if (isset($this->professions[$professionIndex]['certificates'][$certificateIndex])) {
            unset($this->professions[$professionIndex]['certificates'][$certificateIndex]);
            unset($this->professions[$professionIndex]['descriptions'][$certificateIndex]);
            $this->professions[$professionIndex]['certificates'] = array_values($this->professions[$professionIndex]['certificates']);
            $this->professions[$professionIndex]['descriptions'] = array_values($this->professions[$professionIndex]['descriptions']);
        }
    }

    public function getPendingRecords()
    {
        return Customerhistoricaldata::where('created_by', Auth::user()->id)
            ->where('status', 'PENDING')
            ->with('nationality', 'professions.profession', 'professions.registertype', 'professions.tire', 'professions.documents', 'user')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function removeProfession($index)
    {
        unset($this->professions[$index]);
        $this->professions = array_values($this->professions);
    }

    public function updatedProfessions($value, $key)
    {
        // Auto-calculate last_renewal_expire_date when last_renewal_year changes
        // Key format: "professions.0.last_renewal_year"
        if (str_ends_with($key, '.last_renewal_year') && ! empty($value)) {
            $parts = explode('.', $key);
            if (count($parts) >= 3) {
                $index = (int) $parts[1];
                // Calculate expire date as end of the renewal year (December 31st)
                if (isset($this->professions[$index])) {
                    $this->professions[$index]['last_renewal_expire_date'] = $value.'-12-31';
                }
            }
        }
    }

    public function openCreateModal()
    {
        $this->mode = 'single';
        $this->createmodal = true;
        $this->resetForm();
    }

    public function openImportModal()
    {
        $this->mode = 'import';
        $this->importmodal = true;
    }

    public function resetForm()
    {
        $this->name = '';
        $this->surname = '';
        $this->email = '';
        $this->phone = '';
        $this->gender = '';
        $this->identificationnumber = '';
        $this->dob = '';
        $this->identificationtype = 'NATIONAL_ID';
        $this->nationality_id = null;
        $this->address = '';
        $this->placeofbirth = '';
        $this->professions = [];
        $this->addProfession();
    }

    public function save()
    {
        try {
            // Validate certificates separately to avoid validation issues
            $certificateErrors = [];
            foreach ($this->professions as $profIndex => $profession) {
                if (isset($profession['certificates']) && is_array($profession['certificates'])) {
                    foreach ($profession['certificates'] as $certIndex => $certificate) {
                        if ($certificate && ! $certificate->isValid()) {
                            $certificateErrors["professions.{$profIndex}.certificates.{$certIndex}"] = 'Invalid certificate file';
                        }
                    }
                }
            }

            if (! empty($certificateErrors)) {
                foreach ($certificateErrors as $key => $message) {
                    $this->addError($key, $message);
                }

                return;
            }

            $this->validate([
                'name' => 'required|string|max:255',
                'surname' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'required|string',
                'gender' => 'required|in:MALE,FEMALE,OTHER',
                'identificationnumber' => 'required|string',
                'dob' => 'required|date',
                'identificationtype' => 'required|string',
                'nationality_id' => 'required|exists:nationalities,id',
                'address' => 'required|string',
                'placeofbirth' => 'required|string',
                'professions' => 'required|array|min:1',
                'professions.*.profession_id' => 'required|exists:professions,id',
                'professions.*.tire_id' => 'nullable|exists:tires,id',
                'professions.*.registrationnumber' => 'nullable|string',
                'professions.*.registrationyear' => 'nullable|integer',
                'professions.*.practisingcertificatenumber' => 'nullable|string',
                'professions.*.registertype_id' => 'nullable|exists:registertypes,id',
                'professions.*.last_renewal_year' => 'nullable|integer',
                'professions.*.last_renewal_year_cdp_points' => 'nullable|string',
                'professions.*.last_renewal_expire_date' => 'nullable|date',
            ], [
                'professions.*.profession_id.required' => 'Please select a profession for all profession entries.',
                'professions.*.profession_id.exists' => 'Selected profession is invalid.',
            ]);

            // Process certificates for each profession
            $professionsData = [];
            foreach ($this->professions as $profession) {
                $professionData = [
                    'profession_id' => $profession['profession_id'] ?? null,
                    'tire_id' => $profession['tire_id'] ?? null,
                    'registrationnumber' => $profession['registrationnumber'] ?? null,
                    'registrationyear' => $profession['registrationyear'] ?? null,
                    'practisingcertificatenumber' => $profession['practisingcertificatenumber'] ?? null,
                    'registertype_id' => $profession['registertype_id'] ?? null,
                    'last_renewal_year' => $profession['last_renewal_year'] ?? null,
                    'last_renewal_year_cdp_points' => $profession['last_renewal_year_cdp_points'] ?? null,
                    'last_renewal_expire_date' => $profession['last_renewal_expire_date'] ?? null,
                ];

                $certificates = [];
                if (isset($profession['certificates']) && is_array($profession['certificates'])) {
                    foreach ($profession['certificates'] as $index => $certificate) {
                        if ($certificate) {
                            try {
                                $path = $certificate->store('historical-certificates', 's3');
                                $certificates[] = [
                                    'file' => $path,
                                    'description' => $profession['descriptions'][$index] ?? 'Certificate',
                                ];
                            } catch (\Exception $e) {
                                $this->error('Failed to upload certificate: '.$e->getMessage());

                                return;
                            }
                        }
                    }
                }

                $professionData['documents'] = $certificates;
                $professionsData[] = $professionData;
            }

            $data = [
                'name' => $this->name,
                'surname' => $this->surname,
                'email' => $this->email,
                'phone' => $this->phone,
                'gender' => $this->gender,
                'identificationnumber' => $this->identificationnumber,
                'dob' => $this->dob,
                'identificationtype' => $this->identificationtype,
                'nationality_id' => $this->nationality_id,
                'address' => $this->address,
                'placeofbirth' => $this->placeofbirth,
                'accounttype_id' => $this->accounttype_id,
                'professions' => $professionsData,
            ];

            $response = $this->repo->create($data);

            if ($response['status'] == 'success') {
                $this->createmodal = false;
                $this->resetForm();
                $this->success($response['message']);
            } else {
                $this->error($response['message']);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Validation errors are automatically handled by Livewire
            // Don't close modal on validation errors
            throw $e;
        } catch (\Exception $e) {
            $this->error('An error occurred: '.$e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        // Generate CSV template with names instead of IDs
        $headers = [
            'name',
            'surname',
            'email',
            'phone',
            'gender',
            'identificationnumber',
            'dob',
            'identificationtype',
            'nationality',
            'address',
            'placeofbirth',
            'profession',
            'tire',
            'registrationnumber',
            'registrationyear',
            'practisingcertificatenumber',
            'registertype',
            'last_renewal_year',
            'last_renewal_year_cdp_points',
            'last_renewal_expire_date',
        ];

        $filename = 'historical_data_import_template_'.date('Y-m-d').'.csv';
        $filepath = storage_path('app/temp/'.$filename);

        // Create temp directory if it doesn't exist
        if (! is_dir(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        $file = fopen($filepath, 'w');

        // Add instructions row
        fputcsv($file, ['INSTRUCTIONS: Use exact names from the system. Each row represents one person with one profession.']);
        fputcsv($file, ['For multiple professions, create separate rows with the same personal information.']);
        fputcsv($file, []); // Empty row

        // Add headers
        fputcsv($file, $headers);

        // Get example data with actual names
        $exampleNationality = \App\Models\Nationality::first();
        $exampleProfession = \App\Models\Profession::first();
        $exampleTire = null;
        $exampleRegistertype = \App\Models\Registertype::first();

        if ($exampleProfession) {
            $professionTire = \App\Models\ProfessionTire::where('profession_id', $exampleProfession->id)
                ->with('tire')
                ->first();
            if ($professionTire && $professionTire->tire) {
                $exampleTire = $professionTire->tire->name;
            }
        }

        // Add example row with names
        $example = [
            'John',
            'Doe',
            'john.doe@example.com',
            '+263771234567',
            'MALE',
            '12345678A12',
            '1990-01-01',
            'NATIONAL_ID',
            $exampleNationality ? $exampleNationality->name : 'Zimbabwe',
            '123 Main Street, Harare',
            'Harare',
            $exampleProfession ? $exampleProfession->name : 'Example Profession',
            $exampleTire ?? 'Example Tier',
            'REG123456',
            '2020',
            'PC123456',
            $exampleRegistertype ? $exampleRegistertype->name : 'Example Register Type',
            '2023',
            '50',
            '2023-12-31',
        ];
        fputcsv($file, $example);
        fclose($file);

        return response()->download($filepath, $filename)->deleteFileAfterSend(true);
    }

    public function import()
    {
        $this->validate([
            'file' => 'required|file|mimes:csv,txt,xlsx,xls|max:10240',
        ]);

        $path = $this->file->store('historical-data-imports');

        $response = $this->repo->importFromFile($path);

        if ($response['status'] == 'success' || $response['status'] == 'partial') {
            $this->importmodal = false;
            $this->file = null;
            $message = $response['message'];
            if ($response['status'] == 'partial') {
                $this->warning($message);
            } else {
                $this->success($message);
            }
        } else {
            $this->error($response['message']);
        }
    }

    public function render()
    {
        return view('livewire.admin.createhistoricaldata', [
            'nationalities' => $this->getNationalities(),
            'professionsList' => $this->getProfessions(),
            'registertypes' => $this->getRegistertypes(),
            'pendingRecords' => $this->getPendingRecords(),
        ]);
    }
}
