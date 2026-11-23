<?php

namespace App\Services\Whatsapp\Flows;

use App\Interfaces\iexchangerateInterface;
use App\Interfaces\invoiceInterface;
use App\Interfaces\ipaynowInterface;
use App\Interfaces\isuspenseInterface;
use App\Models\Bankaccount;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Paynowtransaction;
use App\Models\WhatsappSession;
use App\Services\Whatsapp\WhatsappClient;

/**
 * Pay an invoice from WhatsApp the same ways the portal allows:
 * from the wallet, online (Paynow top-up), or at the bank with a proof of payment for finance to verify.
 */
class PaymentFlow
{
    use FlowHelpers;

    /** Set when an invoice has just been paid, so the caller can move on to the next step. */
    public ?Invoice $paid = null;

    public function __construct(
        private readonly WhatsappClient $wa,
        private readonly invoiceInterface $invoices,
        private readonly isuspenseInterface $suspense,
        private readonly iexchangerateInterface $rates,
        private readonly ipaynowInterface $paynow,
    ) {}

    /** Show an invoice and the ways to pay it. */
    public function offer(string $phone, Customer $customer, WhatsappSession $session, Invoice $invoice): void
    {
        $invoice = Invoice::with('currency', 'settlementsplit')->find($invoice->id);

        if ($invoice->status === 'PAID') {
            $this->wa->sendText($phone, "Invoice {$invoice->invoice_number} is already paid. Thank you.");
            $this->reset($session);

            return;
        }
        if ($invoice->status === 'AWAITING') {
            $this->wa->sendText($phone, "Invoice {$invoice->invoice_number} has been submitted and is waiting for finance to verify it. We will message you when it is approved.");
            $this->reset($session);

            return;
        }
        if ($invoice->settlementsplit && (float) $invoice->settlementsplit->percentage != 100) {
            $this->wa->sendText($phone, "Invoice {$invoice->invoice_number} is paid in two currencies, which must be done on the portal: ".config('generalutils.base_url'));
            $this->reset($session);

            return;
        }

        $currency = $invoice->currency->name ?? '';
        $balance = $this->balance($invoice);
        $wallet = $this->walletBalance($customer, $invoice);

        $text = "*Invoice {$invoice->invoice_number}*\n".($invoice->description ? mb_substr($invoice->description, 0, 120)."\n" : '')."Amount due: *{$currency} ".$this->money($balance)."*\nYour wallet: {$currency} ".$this->money($wallet);

        $buttons = [];
        if ($wallet + 0.001 >= $balance && $balance > 0) {
            $buttons[] = ['id' => 'pay_wallet', 'title' => 'Pay from wallet'];
        } elseif (config('generalutils.ispaynowactive')) {
            $buttons[] = ['id' => 'pay_online', 'title' => 'Pay online'];
        }
        $buttons[] = ['id' => 'pay_bank', 'title' => 'Paid at bank'];
        $buttons[] = ['id' => 'pay_later', 'title' => 'Pay later'];

        $session->state = 'pay';
        $session->data = ['step' => 'choose', 'invoice_id' => $invoice->id];
        $this->wa->sendButtons($phone, $text, $buttons);
    }

    /** Handle a reply while a payment is in progress. */
    public function handle(string $phone, Customer $customer, WhatsappSession $session, string $input, array $msg): void
    {
        $invoice = Invoice::with('currency')->where('customer_id', $customer->id)->find($session->data['invoice_id'] ?? 0);
        if (! $invoice) {
            $this->reset($session);
            $this->wa->sendText($phone, 'That invoice could not be found. Send *menu* to start again.');

            return;
        }

        $step = $session->data['step'] ?? 'choose';

        if ($input === 'pay_later' || $input === 'later') {
            $this->reset($session);
            $this->wa->sendText($phone, 'No problem. Your invoice stays open. Send *applications* to come back to it.');

            return;
        }

        if ($step === 'pop') {
            $this->receiveProof($phone, $session, $invoice, $msg);

            return;
        }

        switch ($input) {
            case 'pay_wallet':
                $this->payFromWallet($phone, $customer, $session, $invoice);
                break;
            case 'pay_bank':
                $this->bankInstructions($phone, $session, $invoice);
                break;
            case 'pay_online':
                $this->startOnline($phone, $customer, $session, $invoice);
                break;
            case 'pay_check':
            case 'paid':
                $this->checkOnline($phone, $customer, $session, $invoice);
                break;
            default:
                $this->offer($phone, $customer, $session, $invoice);
        }
    }

    // ------------------------------------------------------------------

    private function payFromWallet(string $phone, Customer $customer, WhatsappSession $session, Invoice $invoice): void
    {
        $balance = $this->balance($invoice);
        if ($this->walletBalance($customer, $invoice) + 0.001 < $balance) {
            $this->wa->sendText($phone, 'Your wallet does not have enough for this invoice. Choose *Paid at bank* or *Pay online*.');
            $this->offer($phone, $customer, $session, $invoice);

            return;
        }

        $rate = $this->rates->getlatestrate($invoice->currency_id);
        if (! $rate) {
            $this->wa->sendText($phone, 'There is no exchange rate set for '.($invoice->currency->name ?? 'this currency').' yet, so we cannot take payment here. Please contact MLCSCZ.');

            return;
        }

        $response = $this->invoices->settleinvoice([
            'invoice_id' => $invoice->id,
            'amount' => $balance,
            'currency_id' => $invoice->currency_id,
            'customer_id' => $invoice->customer_id,
            'exchangerate_id' => $rate->id,
        ]);

        if (($response['status'] ?? '') === 'success') {
            $this->reset($session);
            $this->paid = $invoice->fresh();
            $this->wa->sendText($phone, "Payment received. Invoice {$invoice->invoice_number} is paid.");
        } else {
            $this->wa->sendText($phone, 'We could not take the payment: '.($response['message'] ?? 'unknown error').'. Please try again, or pay on the portal.');
        }
    }

    private function bankInstructions(string $phone, WhatsappSession $session, Invoice $invoice): void
    {
        $accounts = Bankaccount::with('bank')->where('currency_id', $invoice->currency_id)->get();
        $lines = ["*Pay at the bank*", '', 'Amount: *'.($invoice->currency->name ?? '').' '.$this->money($this->balance($invoice)).'*'];
        foreach ($accounts as $account) {
            $lines[] = ($account->bank->name ?? 'Bank').': '.preg_replace('/\(.*\)$/', '', $account->account_number);
        }
        $lines[] = '';
        $lines[] = 'Use *'.$invoice->invoice_number.'* as the payment reference.';
        $lines[] = '';
        $lines[] = 'After paying, send a *photo or PDF of your deposit slip or transfer* here and we will pass it to finance to verify. Send *later* if you want to do it afterwards.';

        $session->state = 'pay';
        $session->data = ['step' => 'pop', 'invoice_id' => $invoice->id];
        $this->wa->sendText($phone, implode("\n", $lines));
    }

    private function receiveProof(string $phone, WhatsappSession $session, Invoice $invoice, array $msg): void
    {
        if (empty($msg['media'])) {
            $this->wa->sendText($phone, 'Please send the deposit slip or transfer proof as a photo or PDF, or send *later*.');

            return;
        }

        $path = $this->storeMedia($this->wa, $msg['media']);
        if ($this->isError($path)) {
            $this->wa->sendText($phone, $this->errorText($path));

            return;
        }

        $saved = $this->invoices->createinvoiceproof(['invoice_id' => $invoice->id, 'file' => $path]);
        if (($saved['status'] ?? '') !== 'success') {
            $this->wa->sendText($phone, 'We could not save your proof: '.($saved['message'] ?? 'unknown error').'. Please try again.');

            return;
        }

        $submitted = $this->invoices->submitforverification($invoice->id);
        $this->reset($session);

        if (($submitted['status'] ?? '') === 'success') {
            $this->wa->sendText($phone, "Thank you. Your proof of payment for invoice {$invoice->invoice_number} has been sent to finance for verification. We will message you here when it is approved.");
        } else {
            $this->wa->sendText($phone, 'Your proof was saved, but it could not be submitted: '.($submitted['message'] ?? 'unknown error').'. Please contact MLCSCZ.');
        }
    }

    private function startOnline(string $phone, Customer $customer, WhatsappSession $session, Invoice $invoice): void
    {
        if (! config('generalutils.ispaynowactive')) {
            $this->wa->sendText($phone, 'Online payment is not available right now. Please choose *Paid at bank*.');
            $this->offer($phone, $customer, $session, $invoice);

            return;
        }

        $shortfall = max($this->balance($invoice) - $this->walletBalance($customer, $invoice), 0);
        if ($shortfall <= 0) {
            $this->payFromWallet($phone, $customer, $session, $invoice);

            return;
        }

        $response = $this->paynow->initiatetransaction([
            'currency_id' => $invoice->currency_id,
            'customer_id' => $customer->id,
            'amount' => round($shortfall, 2),
        ]);

        if (($response['status'] ?? '') !== 'success') {
            $this->wa->sendText($phone, 'We could not start the online payment: '.($response['message'] ?? 'unknown error').'. Please choose *Paid at bank*.');

            return;
        }

        $session->state = 'pay';
        $session->data = ['step' => 'online', 'invoice_id' => $invoice->id];
        $this->wa->sendText($phone, "Pay *".($invoice->currency->name ?? '').' '.$this->money($shortfall)."* here:\n".$response['redirecturl']."\n\nWhen you have paid, tap the button below.");
        $this->wa->sendButtons($phone, 'Finished paying?', [['id' => 'pay_check', 'title' => 'I have paid'], ['id' => 'pay_later', 'title' => 'Pay later']]);
    }

    private function checkOnline(string $phone, Customer $customer, WhatsappSession $session, Invoice $invoice): void
    {
        $transaction = Paynowtransaction::where('customer_id', $customer->id)->where('status', 'pending')->orderByDesc('id')->first();
        if ($transaction) {
            $this->paynow->checktransactionbyid($transaction->id);
        }

        if ($this->walletBalance($customer, $invoice) + 0.001 >= $this->balance($invoice)) {
            $this->payFromWallet($phone, $customer, $session, $invoice);

            return;
        }

        $this->wa->sendText($phone, 'We have not received the payment yet. If you have just paid, wait a minute and tap *I have paid* again.');
        $this->wa->sendButtons($phone, 'Finished paying?', [['id' => 'pay_check', 'title' => 'I have paid'], ['id' => 'pay_later', 'title' => 'Pay later']]);
    }

    private function balance(Invoice $invoice): float
    {
        return round((float) $this->invoices->getinvoicebalance($invoice->id, $invoice->currency_id), 2);
    }

    private function walletBalance(Customer $customer, Invoice $invoice): float
    {
        return (float) ($this->suspense->getbalance($customer->id, $invoice->currency_id)['balance'] ?? 0);
    }

    private function reset(WhatsappSession $session): void
    {
        $session->state = null;
        $session->data = null;
    }
}
