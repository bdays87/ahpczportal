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
    public function getinvoicebalance($invoice_id,$currency_id);

    public function settleinvoice($data);
}
