<?php

namespace App\Interfaces;

interface invoiceInterface
{
    public function createInvoice($data);
    public function createrenewalinvoice($data);

    public function createotherapplicationinvoice($data);
    public function getInvoice($id);
    public function deleteInvoice($id);
    public function getinvoicebycustomerprofession($customerprofession_id);
    public function getcustomerprofessioninvoices($customerprofession_id,$type);
    public function getinvoiceproof($invoice_id);
    public function createinvoiceproof($data);
    public function deleteinvoiceproof($id);
    public function submitforverification($invoice_id);
    public function getinvoices($status);
    public function getcustomerinvoices($customer_id);
    public function getpaidinvoices($year, $search, $currency_id = null);
    public function getpaidinvoicetotals($year, $search, $currency_id = null);
    public function getpaidinvoiceyears();
    public function getInvoiceByUuid($uuid);
    public function findduplicatereceipts();
    public function deletereceipt($id);
    public function deleteduplicatereceipts();
    public function getinvoicebalance($invoice_id,$currency_id);

    public function settleinvoice($data);
}
