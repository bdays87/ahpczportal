<?php

namespace App\implementations;

use App\Interfaces\iexchangerateInterface;
use App\Interfaces\igeneralutilsInterface;
use App\Interfaces\invoiceInterface;
use App\Models\Applicationfee;
use App\Models\Applicationtype;
use App\Models\Customer;
use App\Models\Customerapplication;
use App\Models\Customerprofession;
use App\Models\Customerprofessionqualificationassessment;
use App\Models\Customerregistration;
use App\Models\Discount;
use App\Models\Invoice;
use App\Models\Otherapplication;
use App\Models\Otherservice;
use App\Models\Penalty;
use App\Models\Proofofpayment;
use App\Models\Receipt;
use App\Models\Registrationfee;
use App\Models\Renewalfee;
use App\Models\Settlementsplit;
use App\Models\Suspense;
use App\Models\User;
use App\Notifications\ApplicationAwaitingApprovalNotification;
use App\Notifications\InvoiceRegistrationNotification;
use App\Notifications\InvoiceSettled;
use App\Notifications\OtherapplicationAwaitingApprovalNotification;
use App\Notifications\ProofofpaymentApproval;
use App\Notifications\QualificationassesmentAwaitingApprovalNotification;
use App\Notifications\RegistrationAwaitingApprovalNotification;
use App\Notifications\RenewalapprovalNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Restorationfee;
class _invoiceRepository implements invoiceInterface
{
    /**
     * Create a new class instance.
     */
    protected $invoice;

    protected $settlementsplit;

    protected $customerprofession;

    protected $registrationfees;

    protected $applicationfees;

    protected $discounts;

    protected $customer;

    protected $applicationtype;

    protected $customerregistration;

    protected $customerapplication;

    protected $otherservice;

    protected $generalutils;

    protected $proofofpayment;

    protected $receipt;

    protected $renewalfee;

    protected $suspense;

    protected $penalties;

    protected $otherapplication;

    protected $customerprofessionqualificationassessment;

    protected $exchangeraterepo;
    protected $restorationfee;

    public function __construct(Invoice $invoice, Penalty $penalties, Otherapplication $otherapplication, Applicationtype $applicationtype, Discount $discounts, Customer $customer, Receipt $receipt, Settlementsplit $settlementsplit, Customerprofession $customerprofession, Registrationfee $registrationfees, Applicationfee $applicationfees, Customerregistration $customerregistration, Proofofpayment $proofofpayment, Customerapplication $customerapplication, Otherservice $otherservice, igeneralutilsInterface $generalutils, Suspense $suspense, Customerprofessionqualificationassessment $customerprofessionqualificationassessment, iexchangerateInterface $exchangeraterepo, Renewalfee $renewalfee, Restorationfee $restorationfee)
    {
        $this->invoice = $invoice;
        $this->receipt = $receipt;
        $this->settlementsplit = $settlementsplit;
        $this->customerprofession = $customerprofession;
        $this->registrationfees = $registrationfees;
        $this->applicationfees = $applicationfees;
        $this->customerregistration = $customerregistration;
        $this->customerapplication = $customerapplication;
        $this->otherservice = $otherservice;
        $this->generalutils = $generalutils;
        $this->proofofpayment = $proofofpayment;
        $this->customer = $customer;
        $this->suspense = $suspense;
        $this->applicationtype = $applicationtype;
        $this->discounts = $discounts;
        $this->customerprofessionqualificationassessment = $customerprofessionqualificationassessment;
        $this->exchangeraterepo = $exchangeraterepo;
        $this->renewalfee = $renewalfee;
        $this->penalties = $penalties;
        $this->otherapplication = $otherapplication;
        $this->restorationfee = $restorationfee;
    }

    /*
    |--------------------------------------------------------------------------
    | DEPRECATED: createrenewalinvoice (month-based penalty version)
    |--------------------------------------------------------------------------
    | This version computed penalties using diffInMonths() from the last
    | certificate expiry date and matched against a penalty band table.
    | Replaced by the year-based version below which charges penalty per
    | each missed renewal year (years between last approved year and current
    | year, excluding the current year itself).
    |
    | public function createrenewalinvoice($data){ ... }
    |--------------------------------------------------------------------------
    */

    /**
     * Create a renewal invoice with year-based penalty calculation.
     *
     * Penalty logic:
     *  - Find the last APPROVED renewal year for this customer profession.
     *  - Count missed years = renewalyear - lastapprovedyear - 1
     *    (current year excluded — no penalty if renewing within the current period)
     *    e.g. last approved = 2021, renewing for 2025 → missed = 3 (2022, 2023, 2024)
     *  - Fetch a single global penalty multiplier from the penalties table (first record).
     *  - Total penalty = base_fee × multiplier × missed_years
     *    e.g. fee=120, multiplier=2, missed=3 → penalty = 120 × 2 × 3 = 720
     */
    public function createrenewalinvoice($data)
    {
        try {
            $customerprofession = $this->customerprofession
                ->with('profession', 'applications')
                ->find($data['customerprofession_id']);

            if (! $customerprofession) {
                return ['status' => 'error', 'message' => 'Customer profession not found'];
            }

            // Block if an application for this year already exists
            $existingapplication = $this->customerapplication
                ->where('customerprofession_id', $customerprofession->id)
                ->where('year', $data['year'])
                ->first();

            if ($existingapplication) {
                $statusMessages = [
                    'APPROVED' => 'Customer application already approved',
                    'PENDING'  => 'Customer application already pending',
                    'REJECTED' => 'Customer application already rejected',
                ];
                if (isset($statusMessages[$existingapplication->status])) {
                    return ['status' => 'error', 'message' => $statusMessages[$existingapplication->status]];
                }
            }

            $restorefee= $this->restorationfee->where('status', 'active')
                ->first();
            // Resolve renewal fee for this tier / register type / application type
            $renewalfee = $this->renewalfee
                ->where('applicationtype_id', $data['applicationtype_id'])
                ->where('registertype_id', $customerprofession->applications?->last()->registertype_id)
                ->where('tire_id', $customerprofession->tire_id)
                ->first();

            if (! $renewalfee) {
                return ['status' => 'error', 'message' => 'Renewal fee not found'];
            }

            $basefee          = $renewalfee->amount;
            $currency_id      = $renewalfee->currency_id;
            $settlementsplit_id = null;
            $penaltyamount    = 0;
            $discountpercentage = 0;
            $penaltydescription = '';

            // ----------------------------------------------------------------
            // Year-based penalty calculation
            // ----------------------------------------------------------------
            // Get the last APPROVED application year for this profession
            $lastapproved = $this->customerapplication
                ->where('customerprofession_id', $customerprofession->id)
                ->where('status', 'APPROVED')
                ->orderByDesc('year')
                ->first();

            if ($lastapproved) {
                $lastapprovedyear = (int) $lastapproved->year;
                $renewalyear      = (int) $data['year'];

                // Missed years = gap between last approved year and current renewal year,
                // excluding the current year itself (no penalty if renewing within period).
                // e.g. last=2021, renewing=2025 → missed = 2025 - 2021 - 1 = 3 (2022,2023,2024)
                $missedyears = max(0, $renewalyear - $lastapprovedyear - 1);

                if ($missedyears > 0) {
                    // Single global penalty multiplier — no tier/range filtering
                    $penaltyrate = $this->penalties->value('penalty');

                    if ($penaltyrate) {
                        // penalty amount = base fee × multiplier × missed years
                        // e.g. fee=120, rate=2, missed=3 → 120 × 2 × 3 = 720
                        // $penaltyamount      = $basefee * $penaltyrate * $missedyears;
                        $penaltyamount      = ($basefee * $penaltyrate * $missedyears)+$restorefee->amount;
                        $penaltydescription = ' | Late Penalty: '.$missedyears.' missed year(s) x'.$penaltyrate.' each +'.$restorefee->amount.' restoration fee';
                    }
                }
            }

            // ----------------------------------------------------------------
            // Age-based discount (RENEWAL only)
            // ----------------------------------------------------------------
            $applicationtype = $this->applicationtype->find($data['applicationtype_id']);

            if ($applicationtype->name == 'RENEWAL') {
                $customerage = $this->customer
                    ->where('id', $customerprofession->customer_id)
                    ->first()
                    ->getage();

                if ($customerage > 0) {
                    $discount = $this->discounts
                        ->where('tire_id', $customerprofession->profession->tire_id)
                        ->where('lowerlimit', '<=', $customerage)
                        ->where('upperlimit', '>=', $customerage)
                        ->first();

                    if ($discount) {
                        $discountpercentage = $discount->discount;
                    }
                }
            }

            // ----------------------------------------------------------------
            // Create the customer application record
            // ----------------------------------------------------------------
            $customerapplication = $this->customerapplication->create([
                'customerprofession_id' => $customerprofession->id,
                'uuid'                  => Str::uuid()->toString(),
                'customer_id'           => $customerprofession->customer_id,
                'registertype_id'       => $customerprofession->applications?->last()->registertype_id,
                'applicationtype_id'    => $data['applicationtype_id'],
                'year'                  => $data['year'],
                'status'                => 'PENDING',
            ]);

            // ----------------------------------------------------------------
            // Build invoice amount: base + penalty - discount
            // ----------------------------------------------------------------
            $invoiceamount = $basefee + $penaltyamount;
            $description   = 'Renewal'.$penaltydescription;

            if ($discountpercentage > 0) {
                $discountamount = $invoiceamount * $discountpercentage / 100;
                $invoiceamount  = $invoiceamount - $discountamount;
                $description   .= ' | Discount: '.$discountpercentage.'%';
            }

            // ----------------------------------------------------------------
            // Resolve settlement split
            // ----------------------------------------------------------------
            $settlementsplit = $this->settlementsplit
                ->where('employmentlocation_id', $customerprofession->employmentlocation_id)
                ->where('type', $applicationtype->name)
                ->first();

            if ($settlementsplit) {
                $settlementsplit_id = $settlementsplit->id;
            }

            // ----------------------------------------------------------------
            // Create invoice
            // ----------------------------------------------------------------
            $this->invoice->create([
                'source'             => 'customerapplication',
                'source_id'          => $customerapplication->id,
                'customer_id'        => $customerprofession->customer_id,
                'uuid'               => Str::uuid()->toString(),
                'year'               => $data['year'],
                'status'             => 'PENDING',
                'createdby'          => Auth::user()->id,
                'description'        => $description,
                'invoice_number'     => $this->generalutils->generateinvoice($customerapplication->id),
                'amount'             => $invoiceamount,
                'currency_id'        => $currency_id,
                'settlementsplit_id' => $settlementsplit_id,
            ]);

            return ['status' => 'success', 'message' => 'Renewal invoice created successfully', 'data' => $customerapplication->id];

        } catch (\Throwable $th) {
            return ['status' => 'error', 'message' => $th->getMessage()];
        }
    }

    public function createotherapplicationinvoice($data)
    {
        try {
            $this->invoice->create($data);

            return ['status' => 'success', 'message' => 'Other application invoice created successfully'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function createInvoice($data)
    {
        /**
         * 1. check if invoice exists
         * 2. if exists, return error
         * 3. if not exists, check settlement split
         * 4. if settlement split exists, get relevant data
         * 5. if settlement split does not exist, create invoice
         */
        try {

            $customerprofession = $this->customerprofession->where('id', $data['customerprofession_id'])->first();

            if ($data['description'] == 'Registration' || $data['description'] == 'Renewal' || $data['description'] == 'New Application') {
                $settlementsplit = $this->settlementsplit->where('employmentlocation_id', $customerprofession->employmentlocation_id)->where('type', strtolower($data['description']))->first();
                if ($settlementsplit) {
                    $data['settlementsplit_id'] = $settlementsplit->id;
                }
            }
            if ($data['description'] == 'Registration') {
                $registrationfee = $this->registrationfees->where('employmentlocation_id', $customerprofession->employmentlocation_id)->where('customertype_id', $customerprofession->customertype_id)->latest()->first();
                $customerregistration = $this->customerregistration->where('customerprofession_id', $customerprofession->id)->first();
                if ($customerregistration) {
                    return ['status' => 'error', 'message' => 'Customer already registered'];
                }
                $customerregistration = $this->customerregistration->create([
                    'customerprofession_id' => $customerprofession->id,
                    'customer_id' => $customerprofession->customer_id,
                    'year' => $data['year'],

                ]);

                $data['source'] = 'customerprofession';
                $data['source_id'] = $customerprofession->id;
                $data['amount'] = $registrationfee->amount;
                $data['currency_id'] = $registrationfee->currency_id;
            }
            if ($data['description'] == 'New Application') {
                $applicationfee = $this->applicationfees->where('employmentlocation_id', $customerprofession->employmentlocation_id)->where('registertype_id', $customerprofession->registertype_id)->where('name', 'NEW')->latest()->first();
                $this->customerapplication->create([
                    'customerprofession_id' => $customerprofession->id,
                    'uuid' => Str::uuid()->toString(),
                    'customer_id' => $customerprofession->customer_id,
                    'registertype_id' => $customerprofession->registertype_id,
                    'year' => $data['year'],

                ]);

                $data['source'] = 'customerprofession';
                $data['source_id'] = $customerprofession->id;
                $data['amount'] = $applicationfee->amount;
                $data['currency_id'] = $applicationfee->currency_id;
            }

            if ($data['description'] == 'Renewal') {

            }

            if ($data['description'] == 'Qualification Assessment') {
                $otherservice = $this->otherservice->where('name', 'Qualification Assessment')->first();
                $data['amount'] = $otherservice->amount;
                $data['currency_id'] = $otherservice->currency_id;
                $data['source'] = 'customerprofession';
                $data['source_id'] = $customerprofession->id;
                $checkinvoice = $this->invoice->where('source_id', $customerprofession->id)->where('description', 'Qualification Assessment')->first();
                if ($checkinvoice) {
                    return ['status' => 'success', 'message' => 'Qualification Assessment already exists'];
                }
            }

            $data['uuid'] = Str::uuid()->toString();
            $data['createdby'] = Auth::user()->id;
            $data['customer_id'] = $customerprofession->customer_id;
            $data['invoice_number'] = $this->generalutils->generateinvoice($customerprofession->id);
            unset($data['customerprofession_id']);
            $this->invoice->create($data);

            return ['status' => 'success', 'message' => 'Invoice created successfully'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function getInvoice($id)
    {
        return $this->invoice->with('currency', 'customer', 'settlementsplit', 'proofofpayment', 'receipts.exchangerate')->where('id', $id)->first();
    }

    public function deleteInvoice($id)
    {
        return $this->invoice->destroy($id);
    }

    public function getinvoicebalance($invoice_id, $currency_id)
    {
        $invoice = $this->invoice->with('currency', 'customer', 'settlementsplit', 'proofofpayment', 'receipts.exchangerate')->where('id', $invoice_id)->first();

        $totalpaid = $this->computeinvoicebalance($invoice_id)['balance'];
        $totalpayable = $totalpaid;
        if ($invoice->currency_id != $currency_id) {
            $exchangerate = $this->exchangeraterepo->getlatestrate($currency_id);
            if ($exchangerate == null) {
                return $totalpayable;
            }
            if ($invoice->settlementsplit) {
                if ($invoice->settlementsplit->percentage != 100) {
                    $allowedpercentage = 100 - $invoice->settlementsplit->percentage;
                    $totalpayable = ($totalpaid * ($allowedpercentage / 100));
                }
            }
            $totalpayable = $totalpayable * $exchangerate->rate;

        }

        return $totalpayable;
    }

    public function getinvoicebycustomerprofession($customerprofession_id)
    {
        return $this->invoice->with('currency', 'customer', 'settlementsplit', 'receipts.exchangerate')->where('source_id', $customerprofession_id)->where('source', 'customerprofession')->get();
    }

    public function getcustomerprofessioninvoices($customerprofession_id, $type)
    {
        $invoices = $this->invoice->with('currency', 'customer', 'settlementsplit')->where('source_id', $customerprofession_id)
            ->where('source', 'customerprofession')
            ->orWhere('source', 'customerapplication')
            ->where('description', 'like', '%'.$type.'%')
            ->get();
        // dd($invoices);
        if ($invoices->count() == 0) {
            return [];
        }
        $arraydata = [];
        $qualificationassessment = $this->checkqualificationassessment($customerprofession_id);
        $total_invoice = $invoices->sum('amount');
        $total_paid = 0;
        $total_balance = 0;
        $invoice_currency = $invoices->first()->currency->name;
        foreach ($invoices as $invoice) {

            /**
             * if new application  follow these steps
             * 1. check if qualification assessment invoice exists
             * 2. if exists, check if status is pending or awaiting
             * 3. if status is not paid lock payment button
             * 4. if status is paid or there is no qualification assessment  enable registration payment button
             * 5. check if registration invoice is paid  and registration application is approved  enable application payment button
             */

            // check if qualification assessment invoice exists

            $totalpaid = $this->computeinvoicebalance($invoice->id);
            $total_paid += $totalpaid['totalpaid'];
            $total_balance += $totalpaid['balance'];
            $comment = '';

            if ($type == 'New Application') {

                $lockbutton = false;
                if ($qualificationassessment) {
                    if ($qualificationassessment->status != 'PAID') {

                        $lockbutton = true;
                        $comment = 'QA not paid';

                    } else {
                        $checkcustomerprofessionqualificationassessment = $this->customerprofessionqualificationassessment->where('customerprofession_id', $customerprofession_id)->first();
                        if ($checkcustomerprofessionqualificationassessment) {
                            if ($checkcustomerprofessionqualificationassessment->status != 'APPROVED') {
                                $lockbutton = true;
                                $comment = 'Qualification assessment awaiting approval';
                            }
                        }
                    }
                }
                $checkregistration = $invoices->where('description', 'Registration')->first();
                if ($checkregistration) {
                    if ($checkregistration->status != 'PAID') {
                        $lockbutton = true;
                        $comment = 'Registration fee not paid';
                    } else {
                        $checkcustomerregistration = $this->customerregistration->where('customerprofession_id', $customerprofession_id)->first();
                        if ($checkcustomerregistration) {
                            if ($checkcustomerregistration->status != 'APPROVED') {
                                $lockbutton = true;
                                $comment = 'registration awaiting approval';
                            }
                        }

                    }
                }

                $arraydata[] = [
                    'data' => $invoice,
                    'id' => $invoice->id,
                    'created_at' => $invoice->created_at,
                    'invoice_number' => $invoice->invoice_number,
                    'description' => $invoice->description,
                    'settlementsplit' => $invoice->settlementsplit != null ? $invoice->settlementsplit->percentage : 0,
                    'status' => $invoice->status,
                    'amount' => $invoice->amount,
                    'paid' => $totalpaid['totalpaid'],
                    'balance' => $totalpaid['balance'],
                    'currency' => $invoice->currency->name,
                    'button' => $lockbutton ? 'disabled' : 'enabled',
                    'comment' => $comment,
                ];
            }

            if ($type == 'Registration') {
                $lockbutton = false;
                if ($qualificationassessment) {
                    if ($qualificationassessment->status != 'PAID') {

                        $lockbutton = true;
                        $comment = 'Qualification assessment fee not paid';

                    } else {
                        $checkcustomerprofessionqualificationassessment = $this->customerprofessionqualificationassessment->where('customerprofession_id', $customerprofession_id)->first();
                        if ($checkcustomerprofessionqualificationassessment) {
                            if ($checkcustomerprofessionqualificationassessment->status != 'APPROVED') {
                                $lockbutton = true;
                                $comment = 'Qualification assessment awaiting approval';
                            }
                        }
                    }
                }
                $arraydata[] = [
                    'data' => $invoice,
                    'id' => $invoice->id,
                    'created_at' => $invoice->created_at,
                    'invoice_number' => $invoice->invoice_number,
                    'description' => $invoice->description,
                    'settlementsplit' => $invoice->settlementsplit != null ? $invoice->settlementsplit->percentage : 0,
                    'status' => $invoice->status,
                    'amount' => $invoice->amount,
                    'paid' => $totalpaid['totalpaid'],
                    'balance' => $totalpaid['balance'],
                    'currency' => $invoice->currency->name,
                    'button' => $lockbutton ? 'disabled' : 'enabled',
                    'comment' => $comment,
                ];
            }

            if ($type == 'Qualification Assessment') {
                $arraydata[] = [
                    'data' => $invoice,
                    'id' => $invoice->id,
                    'created_at' => $invoice->created_at,
                    'invoice_number' => $invoice->invoice_number,
                    'description' => $invoice->description,
                    'settlementsplit' => $invoice->settlementsplit != null ? $invoice->settlementsplit->percentage : 0,
                    'status' => $invoice->status,
                    'amount' => $invoice->amount,
                    'paid' => $totalpaid['totalpaid'],
                    'balance' => $totalpaid['balance'],
                    'currency' => $invoice->currency->name,
                    'button' => $invoice->status == 'PAID' ? 'disabled' : 'enabled',
                    'comment' => '',
                ];
            }
            if (str_contains($type, 'Renewal')) {
                $arraydata[] = [
                    'data' => $invoice,
                    'id' => $invoice->id,
                    'created_at' => $invoice->created_at,
                    'invoice_number' => $invoice->invoice_number,
                    'description' => $invoice->description,
                    'settlementsplit' => $invoice->settlementsplit != null ? $invoice->settlementsplit->percentage : 0,
                    'status' => $invoice->status,
                    'amount' => $invoice->amount,
                    'paid' => $totalpaid['totalpaid'],
                    'balance' => $totalpaid['balance'],
                    'currency' => $invoice->currency->name,
                    'button' => $invoice->status == 'PAID' ? 'disabled' : 'enabled',
                    'comment' => '',
                ];
            }

        }

        return ['data' => $arraydata, 'invoice_currency' => $invoice_currency, 'total_invoice' => $total_invoice, 'total_paid' => $total_paid, 'total_balance' => $total_balance];
    }

    public function checkqualificationassessment($customerprofession_id)
    {
        $checkqualificationassessment = $this->invoice->where('source_id', $customerprofession_id)->where('description', 'Qualification Assessment')->first();
        if ($checkqualificationassessment) {
            return $checkqualificationassessment;
        }

        return null;
    }

    public function computeinvoicebalance($invoice_id)
    {
        $invoice = $this->invoice->where('id', $invoice_id)->first();
        $totalpaid = $this->receipt->with('exchangerate')->where('invoice_id', $invoice_id)->get();
        $totalpaidamount = 0;
        foreach ($totalpaid as $paid) {
            $totalpaidamount += $paid->amount / $paid->exchangerate->rate;
        }

        return ['totalpaid' => $totalpaidamount, 'balance' => $invoice->amount - $totalpaidamount];
    }

    public function getinvoiceproof($invoice_id)
    {
        return $this->proofofpayment->where('invoice_id', $invoice_id)->get();
    }

    public function createinvoiceproof($data)
    {
        try {
            $invoice = $this->invoice->with('customer')->where('id', $data['invoice_id'])->first();
            $this->proofofpayment->create($data);

            /*$users = User::permission("invoices.receipt")->get();
                    if(count($users) > 0){
                        foreach($users as $user){
                            $user->notify(new ProofofpaymentApproval($invoice));
                        }
                    }*/
            return ['status' => 'success', 'message' => 'Invoice proof created successfully'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function deleteinvoiceproof($id)
    {
        try {
            $proof = $this->proofofpayment->where('id', $id)->first();
            Storage::delete($proof->file);
            $proof->delete();

            return ['status' => 'success', 'message' => 'Invoice proof deleted successfully'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function submitforverification($invoice_id)
    {
        try {
            $invoice = $this->invoice->with('customer')->where('id', $invoice_id)->first();
            if ($invoice->source == 'customerprofession') {
                $customerprofession = $this->customerprofession->where('id', $invoice->source_id)->first();
                $customerprofession->status = 'AWAITING_FINANCE';
                $customerprofession->save();
            }
            $invoice->status = 'AWAITING';
            $invoice->save();
            $users = User::permission('invoices.receipt')->get();
            foreach ($users as $user) {
                $user->notify(new ProofofpaymentApproval($invoice));
            }

            return ['status' => 'success', 'message' => 'Invoice verified successfully'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function getinvoices($status)
    {
        return $this->invoice->with('customer', 'receipts.exchangerate')->where('status', $status)->get();
    }

    public function settleinvoice($data)
    {
        try {
            $receiptnumber = $this->generalutils->generatereceiptnumber($data['invoice_id']);
            $suspenses = $this->suspense->with('receipts.exchangerate')->where('customer_id', $data['customer_id'])->where('currency_id', $data['currency_id'])->where('status', 'PENDING')->get();
            $settled = false;
            foreach ($suspenses as $suspense) {
                $balance = $suspense->amount - $suspense->receipts->sum('amount');

                $amount = $data['amount'];
                if ($balance <= $data['amount']) {
                    $suspense->status = 'UTILIZED';
                    $suspense->save();
                    $amount = $balance;
                }

                $this->receipt->create([
                    'invoice_id' => $data['invoice_id'],
                    'amount' => $amount,
                    'suspense_id' => $suspense->id,
                    'currency_id' => $data['currency_id'],
                    'createdby' => Auth::user()->id,
                    'customer_id' => $data['customer_id'],
                    'exchangerate_id' => $data['exchangerate_id'],
                    'receipt_number' => $receiptnumber,
                    'amount' => $data['amount'],
                ]);
                $checkinvoice = $this->checkinvoicesettlement($data['invoice_id']);

                if ($checkinvoice) {
                    $settled = true;
                    $invoice = $this->invoice->with('customer.customeruser.user')->where('id', $data['invoice_id'])->first();
                    $invoice->status = 'PAID';
                    $invoice->save();
                    if ($invoice->source == 'customerprofession') {
                        $customerprofession = $this->customerprofession->with('profession')->where('id', $invoice->source_id)->first();

                        if ($invoice->description == 'Registration') {
                            $customerregistration = $this->customerregistration->where('customerprofession_id', $customerprofession->id)->first();
                            $customerregistration->status = 'AWAITING';
                            $customerregistration->save();
                            $customerprofession->update(['status' => 'AWAITING_REG']);
                            $user = $invoice->customer->customeruser->user;
                            if ($user) {
                                $user->notify(new InvoiceRegistrationNotification($invoice, $customerprofession->profession));
                                $user = User::permission('registrations.approve')->get();
                                foreach ($user as $u) {
                                    $u->notify(new RegistrationAwaitingApprovalNotification($invoice, $customerprofession->profession));
                                }
                            }

                        } elseif ($invoice->description == 'New Application') {
                            $customerapplication = $this->customerapplication->where('customerprofession_id', $customerprofession->id)->where('status', 'PENDING')->first();
                            $customerapplication->status = 'AWAITING';
                            $customerapplication->save();
                            $customerprofession->update(['status' => 'AWAITING_APP']);
                            $user = User::permission('applications.approve')->get();
                            foreach ($user as $u) {
                                $u->notify(new ApplicationAwaitingApprovalNotification($invoice, $customerprofession->profession));
                            }
                        } elseif ($invoice->description == 'Qualification Assessment') {
                            $customerprofession->status = 'AWAITING_QA';
                            $customerprofession->save();
                            $user = User::permission('assessments.process')->get();
                            foreach ($user as $u) {
                                $u->notify(new QualificationassesmentAwaitingApprovalNotification($invoice, $customerprofession->profession));
                            }
                        }

                    } elseif ($invoice->source == 'customerapplication') {
                        $customerapplication = $this->customerapplication->with('customerprofession.customer')->where('id', $invoice->source_id)->first();
                        $customerapplication->status = 'AWAITING';
                        $customerapplication->save();
                        $user = User::permission('applications.approve')->get();
                        foreach ($user as $u) {
                            $u->notify(new RenewalapprovalNotification($invoice));
                        }
                    } elseif ($invoice->source == 'otherapplication') {
                        $otherapplication = $this->otherapplication->with('customer', 'otherservice')->where('id', $invoice->source_id)->first();
                        if ($otherapplication->otherservice->requireapproval == 'YES') {
                            $otherapplication->status = 'AWAITING';
                            $user = User::permission('applications.approve')->get();
                            foreach ($user as $u) {
                                $u->notify(new OtherapplicationAwaitingApprovalNotification($invoice));
                            }
                        } else {
                            $otherapplication->status = 'APPROVED';
                            $otherapplication->approvedby = Auth::user()->id;
                        }
                        $otherapplication->save();

                    }

                    // // send notification
                    $user = $invoice->customer->customeruser->user;
                    $user->notify(new InvoiceSettled($invoice));

                }

            }

            if ($settled) {
                return ['status' => 'success', 'message' => 'Invoice settled successfully'];
            } else {
                return ['status' => 'success', 'message' => 'Invoice receipted not settled'];
            }
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function checkinvoicesettlement($invoice_id)
    {

        $receipts = $this->receipt->with('exchangerate')->with('invoice')->where('invoice_id', $invoice_id)->get();
        $totalamount = 0;
        foreach ($receipts as $receipt) {
            $totalamount += $receipt->amount / $receipt->exchangerate->rate;
        }

        $invoice = $this->invoice->where('id', $invoice_id)->first();
        if ($totalamount >= $invoice->amount) {
            return true;
        }

        return false;

    }

    public function computetotalpaid($receipts)
    {

        $totalpaid = 0;
        foreach ($receipts as $receipt) {
            $totalpaid += $receipt->amount / $receipt->exchangerate->rate;
        }

        return $totalpaid;
    }
}
