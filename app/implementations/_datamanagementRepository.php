<?php

namespace App\implementations;
use App\Interfaces\idatamanagementInterface;
use App\Models\Customerapplicationimport;
use App\Models\Customerimport;
use App\Models\Customerprofessionimport;
use App\Models\Customerregistrationimport;
use App\Models\Customeruserimport;
use App\Models\Customercdpimports;
use App\Models\Professionimport;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
class _datamanagementRepository implements idatamanagementInterface
{
   /**
     * Create a new class instance.
     */
     protected $professionimport;

protected $customerimport;

protected $customerusrimport;

protected $customerprofessionimport;

protected $customerregistrationimport;

protected $customerapplicationimport;
protected $customercdpimport;

public function __construct(Professionimport $professionimport, Customercdpimports $customercdpimport,Customerimport $customerimport, Customerregistrationimport $customerregistrationimport, Customeruserimport $customerusrimport, Customerprofessionimport $customerprofessionimport, Customerapplicationimport $customerapplicationimport)
{
    $this->professionimport = $professionimport;
    $this->customerimport = $customerimport;
    $this->customerusrimport = $customerusrimport;
    $this->customerprofessionimport = $customerprofessionimport;
    $this->customerregistrationimport = $customerregistrationimport;
    $this->customerapplicationimport = $customerapplicationimport;
    $this->customercdpimport = $customercdpimport;
}

public function getprofessionimports($search)
{
    return $this->professionimport->when($search, function ($query) use ($search) {
        $query->where('name', 'like', "%{$search}%");
    })->paginate(10);
}

public function createprofession($data)
{
    try {
        $check = $this->professionimport->where('prefix', $data['prefix'])->first();
        if ($check) {
            return ['status' => 'error', 'message' => 'Prefix already exists'];
        }
        $this->professionimport->create($data);

        return ['status' => 'success', 'message' => 'Profession created successfully', 'data' => $data];
    } catch (\Exception $e) {
        return ['status' => 'error', 'message' => $e->getMessage()];
    }
}

public function saveprofessionimport($path)
{
    try {
        $file = fopen(Storage::path($path), 'r');
        if ($file === false) {
            return ['status' => 'error', 'message' => 'Failed to open file'];
        }

        $i = 0;
        $data = [];
        $batchSize = 1000;
        while (($row = fgetcsv($file, null, ',')) !== false) {
            if ($i > 0) {
                $data[] = [
                    'name' => $row[0],
                    'prefix' => $row[1],
                    'proceeded' => 'N',
                ];

                // Insert in batches to avoid MySQL placeholder limit
                if (count($data) >= $batchSize) {
                    $this->professionimport->insert($data);
                    $data = [];
                }
            }
            $i++;
        }
        fclose($file);

        // Insert remaining records
        if (! empty($data)) {
            $this->professionimport->insert($data);
        }

        return ['status' => 'success', 'message' => 'Professions imported successfully'];
    } catch (\Exception $e) {
        if (isset($file) && is_resource($file)) {
            fclose($file);
        }

        return ['status' => 'error', 'message' => $e->getMessage()];
    }

}

public function getprofessionimport($id)
{
    return $this->professionimport->find($id);
}

public function updateprofessionimport($id, $data)
{
    try {
        $check = $this->professionimport->where('name', $data['name'])->where('id', '!=', $id)->first();
        if ($check) {
            return ['status' => 'error', 'message' => 'Profession already exists'];
        }
        $this->professionimport->where('id', $id)->update($data);

        return ['status' => 'success', 'message' => 'Profession updated successfully'];
    } catch (\Exception $e) {
        return ['status' => 'error', 'message' => $e->getMessage()];
    }
}

public function deleteprofessionimport($id)
{
    try {
        $check = $this->professionimport->find($id);
        if (! $check) {
            return ['status' => 'error', 'message' => 'Profession not found'];
        }
        if ($check->proceeded == 'Y') {
            return ['status' => 'error', 'message' => 'Profession already proceeded'];
        }
        $check->delete();

        return ['status' => 'success', 'message' => 'Profession deleted successfully'];
    } catch (\Exception $e) {
        return ['status' => 'error', 'message' => $e->getMessage()];
    }
}

public function importcustomers($path)
{
    try {
        $file = fopen(Storage::path($path), 'r');
        if ($file === false) {
            return ['status' => 'error', 'message' => 'Failed to open file'];
        }

        $i = 0;
        $data = [];
        $batchSize = 1000;
        while (($row = fgetcsv($file, null, ',')) !== false) {
            if ($i > 0) {
                $data[] = [
                    'name' => $row[0],
                    'surname' => $row[1],
                    'regnumber' => $row[2],
                    'gender' => $row[3],
                    'email' => $row[4]
                ];



                // Insert in batches to avoid MySQL placeholder limit
                if (count($data) >= $batchSize) {
                    //dd($data);
                    $this->customerimport->insert($data);
                    $data = [];
                }
            }
            $i++;
        }
        fclose($file);

        // Insert remaining records
        if (! empty($data)) {
            $this->customerimport->insert($data);
        }

        return ['status' => 'success', 'message' => 'Customers imported successfully'];
    } catch (\Exception $e) {
        if (isset($file) && is_resource($file)) {
            fclose($file);
        }

        return ['status' => 'error', 'message' => $e->getMessage()];
    }
}

public function getallcustomers($search = null)
{
    return $this->customerimport->when($search, function ($query) use ($search) {
        return $query->where('name', 'like', '%'.$search.'%')
            ->orWhere('surname', 'like', '%'.$search.'%')
            ->orWhere('regnumber', 'like', '%'.$search.'%');
    })->paginate(100);
}

public function getcustomer($id)
{
    return $this->customerimport->where('id', $id)->first();
}

public function createcustomer($data)
{
    try {
        $check = $this->customerimport->where('regnumber', $data['regnumber'])->first();
        if ($check) {
            return ['status' => 'error', 'message' => 'Customer already exists'];
        }
        $this->customerimport->create($data);

        return ['status' => 'success', 'message' => 'Customer created successfully'];
    } catch (\Exception $e) {
        return ['status' => 'error', 'message' => $e->getMessage()];
    }
}

public function updatecustomer($id, $data)
{
    try {
        $check = $this->customerimport->where('regnumber', $data['regnumber'])->where('id', '!=', $id)->first();
        if ($check) {
            return ['status' => 'error', 'message' => 'Customer already exists'];
        }
        $this->customerimport->where('id', $id)->update($data);

        return ['status' => 'success', 'message' => 'Customer updated successfully'];
    } catch (\Exception $e) {
        return ['status' => 'error', 'message' => $e->getMessage()];
    }
}

public function deletecustomer($id)
{
    try {
        $check = $this->customerimport->where('id', $id)->first();
        if (! $check) {
            return ['status' => 'error', 'message' => 'Customer not found'];
        }
        $this->customerimport->where('id', $id)->delete();

        return ['status' => 'success', 'message' => 'Customer deleted successfully'];
    } catch (\Exception $e) {
        return ['status' => 'error', 'message' => $e->getMessage()];
    }
}

public function getallusers($search = null)
{
    return $this->customerusrimport->when($search, function ($query) use ($search) {
        return $query->where('name', 'like', '%'.$search.'%')
            ->orWhere('surname', 'like', '%'.$search.'%')
            ->orWhere('email', 'like', '%'.$search.'%');
    })->paginate(100);
}
public function getuser($id)
{
    return $this->customerusrimport->where('id', $id)->first();
}
public function importusers($path)
{
    try {
        $file = fopen(Storage::path($path), 'r');
        if ($file === false) {
            return ['status' => 'error', 'message' => 'Failed to open file'];
        }

        $i = 0;
        $data = [];
        $batchSize = 1000;
        while (($row = fgetcsv($file, null, ',')) !== false) {
            $tmppassword = $row[4]=="" ? Str::random(10) : $row[4];
            if ($i > 0) {
                $data[] = [
                    'regnumber' => $row[0],
                    'name' => $row[1],
                    'surname' => $row[2],
                    'email' => $row[3],
                    'password' => $row[4]=="" ? $tmppassword : $row[4],
                ];

                // Insert in batches to avoid MySQL placeholder limit
                if (count($data) >= $batchSize) {
                    $this->customerusrimport->insert($data);
                    $data = [];
                }
            }
            $i++;
        }
        fclose($file);

        // Insert remaining records
        if (! empty($data)) {
            $this->customerusrimport->insert($data);
        }

        return ['status' => 'success', 'message' => 'Users imported successfully'];
    } catch (\Exception $e) {
        if (isset($file) && is_resource($file)) {
            fclose($file);
        }

        return ['status' => 'error', 'message' => $e->getMessage()];
    }
}

public function createuser($data)
{
    try {
        $check = $this->customerusrimport->where('email', $data['email'])->first();
        if ($check) {
            return ['status' => 'error', 'message' => 'User already exists'];
        }
        $this->customerusrimport->create($data);

    } catch (\Exception $e) {
        return ['status' => 'error', 'message' => $e->getMessage()];
    }
}

public function updateuser($id, $data)
{
    try {
        $check = $this->customerusrimport->where('email', $data['email'])->where('id', '!=', $id)->first();
        if ($check) {
            return ['status' => 'error', 'message' => 'User already exists'];
        }
        $this->customerusrimport->where('id', $id)->update($data);

        return ['status' => 'success', 'message' => 'User updated successfully'];
    } catch (\Exception $e) {
        return ['status' => 'error', 'message' => $e->getMessage()];
    }
}

public function deleteuser($id)
{
    try {
        $check = $this->customerusrimport->where('id', $id)->first();
        if (! $check) {
            return ['status' => 'error', 'message' => 'User not found'];
        }
        $this->customerusrimport->where('id', $id)->delete();

        return ['status' => 'success', 'message' => 'User deleted successfully'];
    } catch (\Exception $e) {
        return ['status' => 'error', 'message' => $e->getMessage()];
    }
}

public function importcustomerprofessions($path)
{

    try {
        $file = fopen(Storage::path($path), 'r');
        if ($file === false) {
            return ['status' => 'error', 'message' => 'Failed to open file'];
        }

        $i = 0;
        $data = [];
        $batchSize = 1000;
        while (($row = fgetcsv($file, null, ',')) !== false) {
            if ($i > 0) {
                $data[] = [
                    'regnumber' => $row[0],
                    'prefix' => $row[1],
                ];

                // Insert in batches to avoid MySQL placeholder limit
                if (count($data) >= $batchSize) {
                    $this->customerprofessionimport->insert($data);
                    $data = [];
                }
            }
            $i++;
        }
        fclose($file);

        // Insert remaining records
        if (! empty($data)) {
            $this->customerprofessionimport->insert($data);
        }

        return ['status' => 'success', 'message' => 'Customer professions imported successfully'];
    } catch (\Exception $e) {
        if (isset($file) && is_resource($file)) {
            fclose($file);
        }

        return ['status' => 'error', 'message' => $e->getMessage()];
    }

}

public function getallcustomerprofessions($search = null)
{
    return $this->customerprofessionimport->when($search, function ($query) use ($search) {
        return $query->where('regnumber', 'like', '%'.$search.'%')
            ->orWhere('prefix', 'like', '%'.$search.'%');
    })->paginate(100);
}

public function getcustomerprofession($id)
{
    return $this->customerprofessionimport->where('id', $id)->first();
}

public function createcustomerprofession($data)
{
    try {
        $check = $this->customerprofessionimport->where('regnumber', $data['regnumber'])->where('prefix', $data['prefix'])->first();
        if ($check) {
            return ['status' => 'error', 'message' => 'Customer profession already exists'];
        }
        $this->customerprofessionimport->create($data);

        return ['status' => 'success', 'message' => 'Customer profession created successfully'];
    } catch (\Exception $e) {
        return ['status' => 'error', 'message' => $e->getMessage()];
    }
}

public function updatecustomerprofession($id, $data)
{
    try {
        $check = $this->customerprofessionimport->where('regnumber', $data['regnumber'])->where('prefix', $data['prefix'])->where('id', '!=', $id)->first();
        if ($check) {
            return ['status' => 'error', 'message' => 'Customer profession already exists'];
        }
        $this->customerprofessionimport->where('id', $id)->update($data);

        return ['status' => 'success', 'message' => 'Customer profession updated successfully'];
    } catch (\Exception $e) {
        return ['status' => 'error', 'message' => $e->getMessage()];
    }
}

public function deletecustomerprofession($id)
{
    try {
        $check = $this->customerprofessionimport->where('id', $id)->first();
        if (! $check) {
            return ['status' => 'error', 'message' => 'Customer profession not found'];
        }
        if ($check->proceeded == 'Y') {
            return ['status' => 'error', 'message' => 'Customer profession already proceeded'];
        }
        $this->customerprofessionimport->where('id', $id)->delete();

        return ['status' => 'success', 'message' => 'Customer profession deleted successfully'];
    } catch (\Exception $e) {
        return ['status' => 'error', 'message' => $e->getMessage()];
    }

}

public function importcustomerregistrations($path)
{

    try {
        $file = fopen(Storage::path($path), 'r');
        if ($file === false) {
            return ['status' => 'error', 'message' => 'Failed to open file'];
        }

        $i = 0;
        $data = [];
        $batchSize = 1000;
        while (($row = fgetcsv($file, null, ',')) !== false) {
            if ($i > 0) {
                $data[] = [
                    'regnumber' => $row[0],
                    'prefix' => $row[1],
                    'certificatenumber' => $row[2],
                    'registrationdate' => $row[3],
                    'status' => 'APPROVED',
                ];

                // Insert in batches to avoid MySQL placeholder limit
                if (count($data) >= $batchSize) {
                    $this->customerregistrationimport->insert($data);
                    $data = [];
                }
            }
            $i++;
        }
        fclose($file);

        // Insert remaining records
        if (! empty($data)) {
            $this->customerregistrationimport->insert($data);
        }

        return ['status' => 'success', 'message' => 'Customer professions imported successfully'];
    } catch (\Exception $e) {
        if (isset($file) && is_resource($file)) {
            fclose($file);
        }

        return ['status' => 'error', 'message' => $e->getMessage()];
    }

}

public function getallcustomerregistrationimports($search = null)
{
    return $this->customerregistrationimport->when($search, function ($query) use ($search) {
        return $query->where('regnumber', 'like', '%'.$search.'%')
            ->orWhere('prefix', 'like', '%'.$search.'%')
            ->orWhere('certificatenumber', 'like', '%'.$search.'%')
            ->orWhere('registrationdate', 'like', '%'.$search.'%')
            ->orWhere('status', 'like', '%'.$search.'%');
    })->paginate(100);
}

public function getcustomerregistrationimport($id)
{
    return $this->customerregistrationimport->where('id', $id)->first();
}

public function createcustomerregistrationimport($data)
{
    try {
        $check = $this->customerregistrationimport->where('regnumber', $data['regnumber'])->where('prefix', $data['prefix'])->where('certificatenumber', $data['certificatenumber'])->first();
        if ($check) {
            return ['status' => 'error', 'message' => 'Customer registration already exists'];
        }
        $this->customerregistrationimport->create($data);

        return ['status' => 'success', 'message' => 'Customer registration created successfully'];
    } catch (\Exception $e) {
        return ['status' => 'error', 'message' => $e->getMessage()];
    }
}

public function updatecustomerregistrationimport($id, $data)
{
    try {
        $check = $this->customerregistrationimport->where('regnumber', $data['regnumber'])->where('prefix', $data['prefix'])->where('certificatenumber', $data['certificatenumber'])->where('id', '!=', $id)->first();
        if ($check) {
            return ['status' => 'error', 'message' => 'Customer registration already exists'];
        }
        $this->customerregistrationimport->where('id', $id)->update($data);

        return ['status' => 'success', 'message' => 'Customer registration updated successfully'];
    } catch (\Exception $e) {
        return ['status' => 'error', 'message' => $e->getMessage()];
    }
}

public function deletecustomerregistrationimport($id)
{
    try {
        $check = $this->customerregistrationimport->where('id', $id)->first();
        if (! $check) {
            return ['status' => 'error', 'message' => 'Customer registration not found'];
        }
        $this->customerregistrationimport->where('id', $id)->delete();

        return ['status' => 'success', 'message' => 'Customer registration deleted successfully'];
    } catch (\Exception $e) {
        return ['status' => 'error', 'message' => $e->getMessage()];
    }
}

public function importcustomerapplications($path)
{
    try {
        $file = fopen(Storage::path($path), 'r');
        if ($file === false) {
            return ['status' => 'error', 'message' => 'Failed to open file'];
        }

        $i = 0;
        $data = [];
        $batchSize = 1000;
        while (($row = fgetcsv($file, null, ',')) !== false) {
            if ($i > 0) {
                $status = $row[6];

                if ($status == 'APPROVED') {
                    $data[] = [
                        'regnumber' => $row[0],
                        'prefix' => $row[1],
                        'applicationtype' => $row[2],
                        'registertype' => $row[3],
                        'certificatenumber' => $row[4],
                        'registrationdate' => $row[5],
                        'certificateexpirydate' => '31-12-'.$row[7],
                        'year' => $row[7],
                        'status' => $status,
                    ];

                    // Insert in batches to avoid MySQL placeholder limit
                    if (count($data) >= $batchSize) {
                        $this->customerapplicationimport->insert($data);
                        $data = [];
                    }
                }
            }
            $i++;
        }
        fclose($file);

        // Insert remaining records
        if (! empty($data)) {
            $this->customerapplicationimport->insert($data);
        }

        return ['status' => 'success', 'message' => 'Customer applications imported successfully'];
    } catch (\Exception $e) {
        if (isset($file) && is_resource($file)) {
            fclose($file);
        }

        return ['status' => 'error', 'message' => $e->getMessage()];
    }

}

public function getallcustomerapplicationimports($search = null)
{
    return $this->customerapplicationimport->when($search, function ($query) use ($search) {
        return $query->where('regnumber', 'like', '%'.$search.'%')
            ->orWhere('prefix', 'like', '%'.$search.'%')
            ->orWhere('certificatenumber', 'like', '%'.$search.'%')
            ->orWhere('registrationdate', 'like', '%'.$search.'%')
            ->orWhere('status', 'like', '%'.$search.'%');
    })->paginate(100);
}

public function getcustomerapplicationimport($id)
{
    return $this->customerapplicationimport->where('id', $id)->first();
}

public function createcustomerapplicationimport($data)
{
    try {
        $check = $this->customerapplicationimport->where('regnumber', $data['regnumber'])->where('prefix', $data['prefix'])->where('certificatenumber', $data['certificatenumber'])->first();
        if ($check) {
            return ['status' => 'error', 'message' => 'Customer application already exists'];
        }
        $this->customerapplicationimport->create($data);

        return ['status' => 'success', 'message' => 'Customer application created successfully'];
    } catch (\Exception $e) {
        return ['status' => 'error', 'message' => $e->getMessage()];
    }
}

public function updatecustomerapplicationimport($id, $data)
{
    try {
        $check = $this->customerapplicationimport->where('regnumber', $data['regnumber'])->where('prefix', $data['prefix'])->where('certificatenumber', $data['certificatenumber'])->where('id', '!=', $id)->first();
        if ($check) {
            return ['status' => 'error', 'message' => 'Customer application already exists'];
        }
        $this->customerapplicationimport->where('id', $id)->update($data);

        return ['status' => 'success', 'message' => 'Customer application updated successfully'];
    } catch (\Exception $e) {
        return ['status' => 'error', 'message' => $e->getMessage()];
    }
}

public function deletecustomerapplicationimport($id)
{
    try {
        $check = $this->customerapplicationimport->where('id', $id)->first();
        if (! $check) {
            return ['status' => 'error', 'message' => 'Customer application not found'];
        }
        $this->customerapplicationimport->where('id', $id)->delete();

        return ['status' => 'success', 'message' => 'Customer application deleted successfully'];
    } catch (\Exception $e) {
        return ['status' => 'error', 'message' => $e->getMessage()];
    }
}

public function importcustomercdps($path){

    try {
        $file = fopen(Storage::path($path), 'r');
        if ($file === false) {
            return ['status' => 'error', 'message' => 'Failed to open file'];
        }

        $i = 0;
        $data = [];
        $batchSize = 1000;
        while (($row = fgetcsv($file, null, ',')) !== false) {
            if ($i > 0) {

               
                    $data[] = [
                        'regnumber' => $row[0],
                        'points' => $row[1],
                        'year' => $row[2],
                    ];

                    // Insert in batches to avoid MySQL placeholder limit
                    if (count($data) >= $batchSize) {
                        $this->customercdpimport->insert($data);
                        $data = [];
                    }
                
            }
            $i++;
        }
        fclose($file);

        // Insert remaining records
        if (! empty($data)) {
            $this->customercdpimport->insert($data);
        }

        return ['status' => 'success', 'message' => 'Customer cdp imported successfully'];
    } catch (\Exception $e) {
        if (isset($file) && is_resource($file)) {
            fclose($file);
        }

        return ['status' => 'error', 'message' => $e->getMessage()];
    }

}
public function getallcustomercdps($search=null){
    return $this->customercdpimport->when($search, function ($query) use ($search) {
        return $query->where('regnumber', 'like', '%'.$search.'%')
            ->orWhere('points', 'like', '%'.$search.'%')
            ->orWhere('year', 'like', '%'.$search.'%');
    })->paginate(100);
}
public function getcustomercdp($id){
    return $this->customercdpimport->where('id', $id)->first();
}
public function createcustomercdp($data){
    try{
      
        $this->customercdpimport->create($data);
        return ['status' => 'success', 'message' => 'Customer cdp created successfully'];
    }catch(\Exception $e){
        return ['status' => 'error', 'message' => $e->getMessage()];
    }
}
public function updatecustomercdp($id, $data){
    try{
      
        $this->customercdpimport->where('id', $id)->update($data);
        return ['status' => 'success', 'message' => 'Customer cdp updated successfully'];
    }catch(\Exception $e){
        return ['status' => 'error', 'message' => $e->getMessage()];
    }
}
public function deletecustomercdp($id){
    try{
        $check = $this->customercdpimport->where('id', $id)->first();
        if (! $check) {
            return ['status' => 'error', 'message' => 'Customer cdp not found'];
        }
        $this->customercdpimport->where('id', $id)->delete();
        return ['status' => 'success', 'message' => 'Customer cdp deleted successfully'];
    }catch(\Exception $e){
        return ['status' => 'error', 'message' => $e->getMessage()];
    }
}
}
