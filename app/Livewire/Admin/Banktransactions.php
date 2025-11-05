<?php

namespace App\Livewire\Admin;

use App\Interfaces\ibankInterface;
use App\Interfaces\ibanktransactionInterface;
use Illuminate\Support\Str;
use Livewire\Component;
use Mary\Traits\Toast;

class Banktransactions extends Component
{
    use Toast;
    public $search;
    public $id;
    public $banksearch;
    public  $statementreference;
    public $accountnumber;
    public $bank_id;
    public  $source_reference;
    public $description;
    public $transaction_date;
    public $amount;
    public $modal=false;
    public $file;
    public  $breadcrumbs=[];
    public $importmodal=false;

    protected $banktransactionRepository;
    protected $bankRepository;
    public function boot(ibanktransactionInterface $banktransactionRepository,ibankInterface $bankRepository)
    {
        $this->banktransactionRepository = $banktransactionRepository;
        $this->bankRepository = $bankRepository;
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
                'label' => 'Bank Transactions'
            ],
        ];
    }

    public function getbanklist()
    {
        return $this->bankRepository->getAll($this->banksearch);
    }

    public function getbanktransactionlist()
    {
        return $this->banktransactionRepository->getAll($this->search);
    }
    public function getbankaccountlist()
    {
        if($this->bank_id){
            return $this->bankRepository->getaccounts($this->bank_id)->accounts;
        }
        return [];
    }

    public function save(){
        try{
        $this->validate([
            "statementreference"=>"required",
            "accountnumber"=>"required",
            "bank_id"=>"required",
            "source_reference"=>"required",
            "description"=>"required",
            "transaction_date"=>"required",
            "amount"=>"required"
        ]);
        
        if($this->id){
            $this->update();
        }else{
            $this->create();
        }
        $this->reset(['statementreference','accountnumber','bank_id','source_reference','description','transaction_date','amount']);
    }catch(\Exception $e){
        $this->error($e->getMessage());
    }
    }

    public function create(){
        $response = $this->banktransactionRepository->create([
            "statement_reference"=>$this->statementreference,
            "account_number"=>$this->accountnumber,
            "bank_id"=>$this->bank_id,
            "source_reference"=>$this->source_reference,
            "description"=>$this->description,
            "transaction_date"=>$this->transaction_date,
            "amount"=>$this->amount
        ]);
        if($response['status']=='success'){
            $this->success($response['message']);
        }else{
            $this->error($response['message']);
        }
    }

    public function update(){
        $response = $this->banktransactionRepository->update($this->id, [
            "statement_reference"=>$this->statementreference,
            "account_number"=>$this->accountnumber,
            "bank_id"=>$this->bank_id,
            "source_reference"=>$this->source_reference,
            "description"=>$this->description,
            "transaction_date"=>$this->transaction_date,
            "amount"=>$this->amount
        ]);
        if($response['status']=='success'){
            $this->success($response['message']);
        }else{
            $this->error($response['message']);
        }
    }

    public function delete($id){
        $response = $this->banktransactionRepository->delete($id);
        if($response['status']=='success'){
            $this->success($response['message']);
        }else{
            $this->error($response['message']);
        }
    }

    public function edit($id){
        $this->id = $id;
        $banktransaction= $this->banktransactionRepository->get($id);
        $this->statementreference = $banktransaction->statement_reference;
        $this->accountnumber = $banktransaction->account_number;
        $this->bank_id = $banktransaction->bank_id;
        $this->source_reference = $banktransaction->source_reference;
        $this->description = $banktransaction->description;
        $this->transaction_date = $banktransaction->transaction_date;
        $this->amount = $banktransaction->amount;
        $this->modal = true;
    }

    public function importrecords(){
        $this->validate([
            'file' => 'required|file|mimes:csv,xls,xlsx',
        ]);
        $filename = Str::random() . ".csv";
        $path = $this->file->store('banktransactions','public');
        $file = fopen(storage_path('app/public/' . $path), 'r');
        $i=0;
        $data = [];
        while (($row = fgetcsv($file, null, ',')) != false) {
            if($i==0){
                $i++;
                continue;
            }
            $data[] = [
                'referencenumber' => $row[0],
                'statement_reference' => $row[1],
                'source_reference' => $row[2],
                'description' => $row[3],
                'accountnumber' => $row[4],
                'transaction_date' => $row[5],
                'amount' => $row[6],
               
            ];
        }
        $response = $this->banktransactionrepository->import($data);
        if($response['status'] == 'success'){
            $this->success('Bank transactions imported successfully');
            $this->importmodal = false;
        }else{
            $this->error($response['message']);
        }
    }


    public function headers():array{
        return [
            ['key'=>'statement_reference','label'=>'StaRef'],
            ['key'=>'account_number','label'=>'AccNo'],
            ['key'=>'bank.name','label'=>'Bank'],
            ['key'=>'source_reference','label'=>'SrcRef'],
            ['key'=>'description','label'=>'Desc'],
            ['key'=>'transaction_date','label'=>'TxnDate'],
            ['key'=>'amount','label'=>'Amt'],
            ['key'=>'currency.name','label'=>'Cur'],
            ['key'=>'customer.name','label'=>'Cust'],
            ['key'=>'status','label'=>'Status'],
            ['key'=>'action','label'=>'']
        ];
    }

    
    public function render()
    {
        return view('livewire.admin.banktransactions',[
            'banktransactions'=>$this->getbanktransactionlist(),
            'headers'=>$this->headers(),
            'banks'=>$this->getbanklist(),
            'accounts'=>$this->getbankaccountlist()
        ]);
    }
}
