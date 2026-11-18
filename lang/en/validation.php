<?php

// Partial override: Laravel merges this over the framework's default validation messages.
return [
    // Shown when a file upload fails (e.g. file too large for the server, poor connection, or an unsupported file).
    // Livewire passes the raw field path (e.g. "historicalProfessions.0.certificates.0") as :attribute,
    // so we deliberately don't use it here.
    'uploaded' => 'We could not upload this file. Please make sure it is a PDF, JPG or PNG under 10MB, check your internet connection, and try selecting the file again.',
];
