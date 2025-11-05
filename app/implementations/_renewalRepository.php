<?php

namespace App\implementations;

use App\Interfaces\irenewalInterface;
use App\Models\Renewaldocument;
use App\Models\Renewalfee;
use Exception;

class _renewalRepository implements irenewalInterface
{
    /**
     * Create a new class instance.
     */
    protected $renewlfee;
    protected $renewaldocument;
    public function __construct(Renewaldocument $renewldocument,Renewalfee $renewalfee)
    {
        $this->renewaldocument = $renewldocument;
        $this->renewlfee = $renewalfee;
    }
    public function getfees(){
    return $this->renewlfee->with('tire','currency','registertype')->get();
    }

public function getfee($id){
    return $this->renewlfee->find($id);

}
public function createfee(array $data){
  try
  {
    $this->renewlfee->create($data);
    return [
      'success' => true,
      'message' => 'Fee created successfully'
    ];
  }catch(\Exception $e){

    return [
      'success' => false,
      'message' => $e->getMessage()
    ];

  }
}
public function updatefee($id,array $data){
    try
    {
      $record =$this->renewlfee->where('id',$id)->first();
      $record->update($data);
      return [
        'success' => true,
        'message' => 'Fee updated successfully'
      ];
    }
    catch(\Exception $e){
      return [
        'success' => false,
        'message' => $e->getMessage()
      ];
    }
  }



public function deletefee($id){
    try
    {
      $record =$this->renewlfee->find($id)->first();
      $record->delete();
      return [
        'success' => true,
        'message' => 'Fee deleted successfully'
      ];
    }
    catch(\Exception $e){
      return [
        'success' => false,
        'message' => $e->getMessage()
      ];
    }
  

}

public function getdocuments(){
    return $this->renewaldocument->with('tire','registertype','document')->get();

}
public function getdocument($id){
    return $this->renewaldocument->find($id);
}
public function createdocument(array $data){
  try
  {
    $this->renewaldocument->create($data);
    return [
      'success' => true,
      'message' => 'Document created successfully'
    ];
  }
  catch(\Exception $e){
    return [
      'success' => false,
      'message' => $e->getMessage()
    ];
  }
}
public function updatedocument($id,array $data){
    try
    {
      $record =$this->renewaldocument->find($id)->first();
      $record->update($data);
      return [
        'success' => true,
        'message' => 'Document updated successfully'
      ];
    }
    catch(\Exception $e){
      return [
        'success' => false,
        'message' => $e->getMessage()
      ];
    }

}
public function deletedocument($id){    
  try
  {
    $record =$this->renewaldocument->find($id)->first();
    $record->delete();
    return [
      'success' => true,
      'message' => 'Document deleted successfully'
    ];
  }
  catch(\Exception $e){
    return [
      'success' => false,
      'message' => $e->getMessage()
    ];
  }
}
}
