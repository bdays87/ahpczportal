<?php

namespace App\Interfaces;

interface igeneralutilsInterface
{
    public function generateregistrationnumber();
    public function getregistrationnumber();
    public function generateinvoice($id);
    public function generatereceiptnumber($id);
    public function generatecertificatenumber($year,$prefix,$id);
}
