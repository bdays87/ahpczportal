<?php

namespace App\Livewire\Admin;

use App\Interfaces\ibankInterface;
use App\Interfaces\ibanktransactionInterface;
use App\Services\BankStatementParser;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Mary\Traits\Toast;

class Banktransactions extends Component
{
    use Toast, WithFileUploads, WithPagination;
    
    public $search = '';
    public $customerSearch = '';
    public $statusFilter = 'all'; // all, PENDING, CLAIMED
    public $id;
    public $customer_id;
    public $banksearch;
    public  $statementreference;
    public $accountnumber;
    public $bank_id;
    public  $source_reference;
    public $description;
    public $transaction_date;
    public $amount;
    public $modal=false;
    public $claimModal=false;
    public $file;
    public  $breadcrumbs=[];
    public $importmodal=false;
    public $bankType = 'Generic';
    public $previewData = [];
    public $importStep = 1; // 1=upload, 2=preview, 3=import
    public $selectedTransactions = [];

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
        $query = \App\Models\Banktransaction::query()
            ->with(['bank', 'currency', 'customer'])
            ->when($this->search, function($q) {
                $q->where(function($query) {
                    $query->where('statement_reference', 'like', '%'.$this->search.'%')
                        ->orWhere('source_reference', 'like', '%'.$this->search.'%')
                        ->orWhere('description', 'like', '%'.$this->search.'%')
                        ->orWhere('account_number', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->statusFilter !== 'all', function($q) {
                $q->where('status', $this->statusFilter);
            })
            ->when($this->customerSearch, function($q) {
                $q->whereHas('customer', function($query) {
                    $query->where('name', 'like', '%'.$this->customerSearch.'%')
                        ->orWhere('surname', 'like', '%'.$this->customerSearch.'%')
                        ->orWhere('email', 'like', '%'.$this->customerSearch.'%');
                });
            })
            ->orderByDesc('transaction_date')
            ->orderByDesc('created_at');
            
        return $query->paginate(20);
    }
    
    public function getCustomerList()
    {
        return \App\Models\Customer::query()
            ->when($this->customerSearch, function($q) {
                $q->where('name', 'like', '%'.$this->customerSearch.'%')
                    ->orWhere('surname', 'like', '%'.$this->customerSearch.'%')
                    ->orWhere('email', 'like', '%'.$this->customerSearch.'%');
            })
            ->limit(50)
            ->get()
            ->map(fn($c) => [
                'id' => $c->id,
                'name' => $c->name . ' ' . $c->surname . ' (' . $c->email . ')'
            ]);
    }
    
    public function updatingSearch()
    {
        $this->resetPage();
    }
    
    public function updatingStatusFilter()
    {
        $this->resetPage();
    }
    
    public function updatingCustomerSearch()
    {
        $this->resetPage();
    }
    
    public function claimTransaction($id)
    {
        $this->id = $id;
        $this->claimModal = true;
    }
    
    public function saveClaimCustomer()
    {
        $this->validate([
            'customer_id' => 'required|exists:customers,id'
        ]);
        
        try {
            $response = $this->banktransactionRepository->update($this->id, [
                'customer_id' => $this->customer_id,
                'status' => 'CLAIMED'
            ]);
            
            if($response['status'] == 'success') {
                $this->success('Transaction claimed successfully');
                $this->claimModal = false;
                $this->reset(['id', 'customer_id']);
            } else {
                $this->error($response['message']);
            }
        } catch(\Exception $e) {
            $this->error($e->getMessage());
        }
    }
    
    public function unclaimTransaction($id)
    {
        try {
            $response = $this->banktransactionRepository->update($id, [
                'customer_id' => null,
                'status' => 'PENDING'
            ]);
            
            if($response['status'] == 'success') {
                $this->success('Transaction unclaimed successfully');
            } else {
                $this->error($response['message']);
            }
        } catch(\Exception $e) {
            $this->error($e->getMessage());
        }
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

    /**
     * Step 1: Upload and parse the file
     */
    public function uploadStatement()
    {
        $this->validate([
            'file' => 'required|file|mimes:csv,xls,xlsx|max:10240',
            'bankType' => 'required',
            'bank_id' => 'required',
        ]);

        try {
            // Get the temporary file path from Livewire
            $tempFilePath = $this->file->getRealPath();
            
            if (!file_exists($tempFilePath)) {
                throw new \Exception('Temporary file not found. Please try uploading again.');
            }

            $parser = new BankStatementParser();
            $result = $parser->parse($tempFilePath, $this->bankType);

            $this->previewData = $result;
            $this->importStep = 2;

            // Select all by default
            $this->selectedTransactions = array_keys($result['transactions']);

            $this->success('Statement parsed successfully! ' . count($result['transactions']) . ' transactions found.');
        } catch (\Exception $e) {
            $this->error('Failed to parse statement: ' . $e->getMessage());
            \Log::error('Bank statement parse error: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Step 2: Review and confirm import
     */
    public function confirmImport()
    {
        if (empty($this->selectedTransactions)) {
            $this->error('Please select at least one transaction to import');
            return;
        }

        $this->importStep = 3;
    }

    /**
     * Step 3: Import selected transactions
     */
    public function executeImport()
    {
        try {
            $imported = 0;
            $skipped = 0;
            $errors = [];

            foreach ($this->selectedTransactions as $index) {
                if (!isset($this->previewData['transactions'][$index])) {
                    continue;
                }

                $txn = $this->previewData['transactions'][$index];

                try {
                    $this->banktransactionRepository->create([
                        'statement_reference' => $txn['reference'],
                        'account_number' => $this->previewData['metadata']['account_number'] ?? '',
                        'bank_id' => $this->bank_id,
                        'currency_id' => 1, // Default to first currency, should be configurable
                        'source_reference' => $txn['reference'],
                        'description' => $txn['description'],
                        'transaction_date' => $txn['transaction_date'],
                        'amount' => abs($txn['amount']),
                        'status' => 'PENDING',
                    ]);
                    $imported++;
                } catch (\Exception $e) {
                    $skipped++;
                    $errors[] = "Row " . ($index + 1) . ": " . $e->getMessage();
                }
            }

            $this->success("Import completed! {$imported} transactions imported, {$skipped} skipped.");
            
            if (!empty($errors)) {
                foreach (array_slice($errors, 0, 5) as $error) {
                    $this->warning($error);
                }
            }

            $this->resetImport();
            $this->importmodal = false;
        } catch (\Exception $e) {
            $this->error('Import failed: ' . $e->getMessage());
        }
    }

    /**
     * Reset import state
     */
    public function resetImport()
    {
        $this->reset(['file', 'bankType', 'previewData', 'importStep', 'selectedTransactions']);
        $this->bankType = 'Generic';
        $this->importStep = 1;
    }

    /**
     * Toggle transaction selection
     */
    public function toggleTransaction($index)
    {
        if (in_array($index, $this->selectedTransactions)) {
            $this->selectedTransactions = array_diff($this->selectedTransactions, [$index]);
        } else {
            $this->selectedTransactions[] = $index;
        }
    }

    /**
     * Select all transactions
     */
    public function selectAll()
    {
        $this->selectedTransactions = array_keys($this->previewData['transactions'] ?? []);
    }

    /**
     * Deselect all transactions
     */
    public function deselectAll()
    {
        $this->selectedTransactions = [];
    }

    /**
     * Get supported banks for dropdown
     */
    public function getSupportedBanks()
    {
        $banks = BankStatementParser::getSupportedBanks();
        return collect($banks)->map(fn($name, $code) => ['id' => $code, 'name' => $name])->values()->toArray();
    }


    public function headers():array{
        return [
            ['key'=>'transaction_date','label'=>'Date'],
            ['key'=>'statement_reference','label'=>'Ref'],
            ['key'=>'description','label'=>'Description'],
            ['key'=>'bank.name','label'=>'Bank'],
            ['key'=>'amount','label'=>'Amount'],
            ['key'=>'customer.name','label'=>'Customer'],
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
            'accounts'=>$this->getbankaccountlist(),
            'supportedBanks'=>$this->getSupportedBanks(),
            'customers'=>$this->getCustomerList(),
        ]);
    }
}
