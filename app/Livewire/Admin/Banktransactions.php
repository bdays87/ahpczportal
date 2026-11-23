<?php

namespace App\Livewire\Admin;

use App\Interfaces\ibankInterface;
use App\Interfaces\ibanktransactionInterface;
use App\Interfaces\iexchangerateInterface;
use App\Interfaces\invoiceInterface;
use App\Interfaces\isuspenseInterface;
use App\Models\Banktransaction as BanktransactionModel;
use App\Models\Bankaccount;
use App\Models\Customer;
use App\Models\Invoice;
use App\Services\BankStatementParser;
use Illuminate\Support\Facades\DB;
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
    public $bankType = 'Template';
    public $previewData = [];
    public $importStep = 1; // 1=upload, 2=preview, 3=import
    public $selectedTransactions = [];

    protected $banktransactionRepository;
    protected $bankRepository;
    protected $invoicerepo;
    protected $suspenserepo;
    protected $exchangeraterepo;
    public function boot(ibanktransactionInterface $banktransactionRepository,ibankInterface $bankRepository,invoiceInterface $invoicerepo,isuspenseInterface $suspenserepo,iexchangerateInterface $exchangeraterepo)
    {
        $this->banktransactionRepository = $banktransactionRepository;
        $this->bankRepository = $bankRepository;
        $this->invoicerepo = $invoicerepo;
        $this->suspenserepo = $suspenserepo;
        $this->exchangeraterepo = $exchangeraterepo;
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
            // Claiming puts the deposit into the customer's wallet (suspense) so they can settle invoices themselves
            $response = $this->banktransactionRepository->creditwallet($this->id, $this->customer_id);

            if($response['status'] == 'success') {
                $this->success($response['message']);
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
            $response = $this->banktransactionRepository->unclaim($id);

            if($response['status'] == 'success') {
                $this->success($response['message']);
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
            'file' => 'required|file|mimes:csv,txt|max:10240',
            'bankType' => 'required',
        ], [
            'file.mimes' => 'Please upload a CSV file. In Excel use Save As, then CSV UTF-8.',
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
            $this->matchInvoices();
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
        $imported = 0;
        $settled = 0;
        $credited = 0;
        $errors = [];

        foreach ($this->selectedTransactions as $index) {
            $txn = $this->previewData['transactions'][$index] ?? null;
            if (! $txn) {
                continue;
            }

            $label = 'Row '.($index + 1).': ';
            $bankaccount = $this->findBankAccount($txn);
            if (! $bankaccount) {
                $errors[] = $label.'bank account '.($txn['account_number'] ?: '(none)').' is not set up';
                continue;
            }

            $statementreference = $txn['reference'];
            $sourcereference = $txn['source_reference'] ?? $txn['reference'];
            if (BanktransactionModel::where('statement_reference', $statementreference)->where('source_reference', $sourcereference)->exists()) {
                $errors[] = $label.'already imported';
                continue;
            }

            $banktransaction = BanktransactionModel::create([
                'statement_reference' => $statementreference,
                'account_number' => $bankaccount->account_number,
                'bank_id' => $bankaccount->bank_id,
                'currency_id' => $bankaccount->currency_id,
                'source_reference' => $sourcereference,
                'description' => $txn['description'],
                'transaction_date' => $txn['transaction_date'],
                'amount' => abs($txn['amount']),
                'status' => 'PENDING',
            ]);
            $imported++;

            $match = $txn['match'] ?? null;

            // Exact invoice match: credit the wallet and settle the invoice
            if ($match && $match['settle']) {
                try {
                    $this->settleFromTransaction($banktransaction, $match);
                    $settled++;
                } catch (\Exception $e) {
                    $errors[] = $label.'imported, not settled: '.$e->getMessage();
                }
                continue;
            }

            // Customer known but no exact invoice match: just credit their wallet, they settle in the portal
            if ($match && ! empty($match['credit_customer_id'])) {
                $response = $this->banktransactionRepository->creditwallet($banktransaction->id, $match['credit_customer_id']);
                if ($response['status'] == 'success') {
                    $credited++;
                } else {
                    $errors[] = $label.'imported, wallet not credited: '.$response['message'];
                }
                continue;
            }

            if ($match && $match['invoice_id']) {
                $errors[] = $label.'imported, not settled: '.$match['reason'];
            }
        }

        $this->success("Import completed! {$imported} transactions imported, {$settled} invoices settled, {$credited} wallets credited.");

        foreach (array_slice($errors, 0, 10) as $error) {
            $this->warning($error);
        }

        $this->resetImport();
        $this->importmodal = false;
    }

    /**
     * Link the bank transaction to the customer, credit their wallet, and settle the invoice from it.
     * Runs in a DB transaction so a failed settlement leaves no partial receipt behind.
     */
    private function settleFromTransaction(BanktransactionModel $banktransaction, array $match): void
    {
        $invoice = Invoice::findOrFail($match['invoice_id']);
        $exchangerate = $this->exchangeraterepo->getlatestrate($invoice->currency_id);
        if (! $exchangerate) {
            throw new \Exception('no exchange rate set for '.$invoice->currency->name);
        }

        DB::transaction(function () use ($banktransaction, $invoice, $match, $exchangerate) {
            $banktransaction->update(['status' => 'CLAIMED', 'customer_id' => $invoice->customer_id]);

            $suspense = $this->suspenserepo->createSuspense([
                'customer_id' => $invoice->customer_id,
                'currency_id' => $invoice->currency_id,
                'amount' => $banktransaction->amount,
                'source' => 'bank',
                'uuid' => Str::uuid()->toString(),
                'source_id' => $banktransaction->id,
                'status' => 'PENDING',
                'createdby' => auth()->id(),
            ]);
            if (($suspense['status'] ?? '') != 'success') {
                throw new \Exception($suspense['message'] ?? 'could not credit the customer wallet');
            }

            $response = $this->invoicerepo->settleinvoice([
                'invoice_id' => $invoice->id,
                'amount' => $match['balance'],
                'currency_id' => $invoice->currency_id,
                'customer_id' => $invoice->customer_id,
                'exchangerate_id' => $exchangerate->id,
            ]);
            if (($response['status'] ?? '') != 'success' || $invoice->fresh()->status != 'PAID') {
                throw new \Exception($response['message'] ?? 'invoice was not settled');
            }
        });
    }

    /**
     * Find the unpaid invoice each parsed row refers to and whether it can be settled automatically
     */
    private function matchInvoices(): void
    {
        foreach ($this->previewData['transactions'] as $index => $txn) {
            $this->previewData['transactions'][$index]['match'] = $this->findSettlement($txn);
        }
    }

    private function findSettlement(array $txn): array
    {
        $none = ['invoice_id' => null, 'invoice_number' => null, 'balance' => 0, 'settle' => false, 'reason' => '', 'credit_customer_id' => null, 'credit_customer' => null];

        if ($txn['amount'] <= 0) {
            return array_merge($none, ['reason' => 'debit, not a payment']);
        }

        $bankaccount = $this->findBankAccount($txn);
        if (! $bankaccount) {
            return array_merge($none, ['reason' => 'bank account is not set up']);
        }

        // Invoice number in the row: the strongest match
        $text = implode(' ', [$txn['description'] ?? '', $txn['reference'] ?? '', $txn['source_reference'] ?? '']);
        // Current format INV20261114<n>, or the old INV-2026-1234-5 format on invoices not yet renumbered
        if (preg_match('/INV20261114\d+|INV-\d{4}-\d+-\d+/i', $text, $found)) {
            $number = strtoupper($found[0]);
            $invoice = Invoice::where('invoice_number', $number)->whereIn('status', ['PENDING', 'AWAITING'])->first();
            if (! $invoice) {
                return array_merge($none, ['invoice_number' => $number, 'reason' => "$number is not an unpaid invoice"]);
            }

            $check = $this->checkInvoiceAmount($invoice, $txn, $bankaccount, $none);
            if ($check['settle']) {
                return $check;
            }

            // The invoice number tells us who paid, even if the amount is partial, an overpayment or another currency:
            // credit their wallet and let them settle in the portal.
            $payer = $invoice->customer;
            if ($payer) {
                return $this->walletCredit($payer, $check, 'invoice '.$number.': '.$check['reason']);
            }

            return $check;
        }

        // Registration number in the row (the customer code): settle an exact invoice, otherwise credit the wallet
        $byregistration = $this->findSettlementByRegistration($txn, $bankaccount, $none);
        if ($byregistration !== null) {
            return $byregistration;
        }

        // Last resort: the customer's name
        return $this->findSettlementByName($txn, $bankaccount, $none);
    }

    /**
     * Mark a row as "credit this customer's wallet" instead of settling an invoice.
     */
    private function walletCredit(Customer $customer, array $base, string $why): array
    {
        return array_merge($base, [
            'settle' => false,
            'credit_customer_id' => $customer->id,
            'credit_customer' => trim($customer->name.' '.$customer->surname),
            'reason' => 'will credit wallet of '.trim($customer->name.' '.$customer->surname).' ('.$why.')',
        ]);
    }

    /**
     * A registration number (customer code) appearing as its own word in the row identifies the customer.
     * Exactly one customer must own it. Short numbers are ignored so years, dates and amounts cannot match.
     * Returns null when the row carries no registration number.
     */
    private function findSettlementByRegistration(array $txn, Bankaccount $bankaccount, array $none): ?array
    {
        $text = strtoupper(implode(' ', [$txn['description'] ?? '', $txn['reference'] ?? '', $txn['source_reference'] ?? '']));
        // Every way the number could appear in a bank reference. A profession number such as "CLIN SCIE527705"
        // may come through with the space, without it ("CLINSCIE527705"), or as just the number ("527705").
        $parts = array_values(array_filter(preg_split('/[^A-Z0-9]+/', $text)));
        $candidates = [];
        foreach ($parts as $i => $part) {
            $candidates[] = $part;
            if (preg_match('/(\d{5,})$/', $part, $digits)) {
                $candidates[] = $digits[1];
            }
            if (isset($parts[$i + 1])) {
                $candidates[] = $part.$parts[$i + 1];
            }
            if (isset($parts[$i + 2])) {
                $candidates[] = $part.$parts[$i + 1].$parts[$i + 2];
            }
        }
        $tokens = array_values(array_unique(array_filter($candidates, fn ($token) => strlen($token) >= 5)));
        if (empty($tokens)) {
            return null;
        }

        $placeholders = implode(',', array_fill(0, count($tokens), '?'));
        $customerids = \App\Models\Customerprofession::whereRaw("REPLACE(registrationnumber, ' ', '') IN ({$placeholders})", $tokens)->pluck('customer_id')
            ->merge(Customer::whereIn('regnumber', $tokens)->pluck('id'))
            ->unique()->values();

        if ($customerids->isEmpty()) {
            return null;
        }
        if ($customerids->count() > 1) {
            return array_merge($none, ['reason' => 'registration number in the row belongs to '.$customerids->count().' customers, claim manually']);
        }

        $customer = Customer::find($customerids->first());
        if (! $customer) {
            return null;
        }

        // Exactly one unpaid invoice in this currency equal to the amount: settle it
        $matches = [];
        $invoices = Invoice::where('customer_id', $customer->id)->whereIn('status', ['PENDING', 'AWAITING'])
            ->where('currency_id', $bankaccount->currency_id)->get();
        foreach ($invoices as $invoice) {
            $check = $this->checkInvoiceAmount($invoice, $txn, $bankaccount, $none);
            if ($check['settle']) {
                $matches[] = $check;
            }
        }
        if (count($matches) == 1) {
            return array_merge($matches[0], ['reason' => 'matched by registration number: '.trim($customer->name.' '.$customer->surname)]);
        }

        return $this->walletCredit($customer, $none, 'matched by registration number, no single invoice equals this amount');
    }

    /**
     * Settle only when the customer's name appears in the row, it is the only customer with that name,
     * and exactly one of their unpaid invoices in this currency equals the amount.
     */
    private function findSettlementByName(array $txn, Bankaccount $bankaccount, array $none): array
    {
        $haystack = ' '.$this->normalizeName(implode(' ', [$txn['description'] ?? '', $txn['reference'] ?? '', $txn['source_reference'] ?? ''])).' ';

        $invoices = Invoice::with('customer')
            ->whereIn('status', ['PENDING', 'AWAITING'])
            ->where('currency_id', $bankaccount->currency_id)
            ->get();

        $customers = $invoices->pluck('customer')->filter(function ($customer) use ($haystack) {
            if (! $customer || trim($customer->name) === '' || trim($customer->surname) === '') {
                return false;
            }
            $forward = ' '.$this->normalizeName($customer->name.' '.$customer->surname).' ';
            $reverse = ' '.$this->normalizeName($customer->surname.' '.$customer->name).' ';

            return str_contains($haystack, $forward) || str_contains($haystack, $reverse);
        })->unique('id');

        if ($customers->isEmpty()) {
            return array_merge($none, ['reason' => 'no customer name in the row matches an unpaid invoice']);
        }
        if ($customers->count() > 1) {
            return array_merge($none, ['reason' => 'name matches '.$customers->count().' customers, settle manually']);
        }

        $customer = $customers->first();
        $matches = [];
        foreach ($invoices->where('customer_id', $customer->id) as $invoice) {
            $check = $this->checkInvoiceAmount($invoice, $txn, $bankaccount, $none);
            if ($check['settle']) {
                $matches[] = $check;
            }
        }

        if (count($matches) == 1) {
            return array_merge($matches[0], ['reason' => 'matched by name: '.$customer->name.' '.$customer->surname]);
        }
        if (count($matches) > 1) {
            return array_merge($none, ['reason' => count($matches).' invoices for '.$customer->name.' '.$customer->surname.' equal this amount, settle manually']);
        }

        return array_merge($none, ['reason' => $customer->name.' '.$customer->surname.' has no unpaid invoice for this amount']);
    }

    /**
     * Settle only when the row amount equals the invoice balance in the account's currency
     */
    private function checkInvoiceAmount(Invoice $invoice, array $txn, Bankaccount $bankaccount, array $none): array
    {
        $result = array_merge($none, ['invoice_id' => $invoice->id, 'invoice_number' => $invoice->invoice_number]);

        if ($bankaccount->currency_id != $invoice->currency_id) {
            return array_merge($result, ['reason' => 'account currency differs from the invoice']);
        }

        $balance = round((float) $this->invoicerepo->getinvoicebalance($invoice->id, $invoice->currency_id), 2);
        $result['balance'] = $balance;
        if (abs($txn['amount'] - $balance) > 0.01) {
            return array_merge($result, ['reason' => 'amount '.number_format($txn['amount'], 2).' differs from balance '.number_format($balance, 2).' (partial payment, claim manually)']);
        }

        return array_merge($result, ['settle' => true, 'reason' => 'will settle']);
    }

    private function normalizeName(string $value): string
    {
        return trim(preg_replace('/\s+/', ' ', preg_replace('/[^a-z ]/', ' ', strtolower($value))));
    }

    private function findBankAccount(array $txn): ?Bankaccount
    {
        $accountnumber = $txn['account_number'] ?? $this->previewData['metadata']['account_number'] ?? '';

        if (! $accountnumber) {
            return null;
        }

        // Stored numbers can carry a currency suffix, e.g. "21573786246(USD)"
        return Bankaccount::where('account_number', $accountnumber)
            ->orWhere('account_number', 'like', $accountnumber.'(%')
            ->first();
    }

    /**
     * Download the CSV template banks and staff fill in for import
     */
    public function downloadTemplate()
    {
        return response()->streamDownload(function () {
            echo BankStatementParser::templateCsv();
        }, 'bank-statement-template.csv', ['Content-Type' => 'text/csv']);
    }

    /**
     * Reset import state
     */
    public function resetImport()
    {
        $this->reset(['file', 'bankType', 'previewData', 'importStep', 'selectedTransactions']);
        $this->bankType = 'Template';
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
