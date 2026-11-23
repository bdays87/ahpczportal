<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;

/**
 * Public practitioner guide: a page anyone can read without signing in, and the same text as a PDF.
 */
class GuideController extends Controller
{
    public function show()
    {
        return view('guide.practitioner');
    }

    public function download()
    {
        $filename = strtolower(preg_replace('/[^A-Za-z0-9]+/', '-', config('app.name'))).'-practitioner-guide.pdf';

        return Pdf::loadView('guide.practitioner-pdf')->setPaper('a4')->download($filename);
    }
}
