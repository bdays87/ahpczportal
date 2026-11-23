<?php

return [
    // Set AUDIT_ENABLED=false in .env to switch the audit log off (not recommended).
    'enabled' => env('AUDIT_ENABLED', true),

    // At most this many rows are recorded for one statement (a bulk update or delete can touch thousands).
    'max_rows' => 200,

    // Tables that are never logged: the log itself, framework plumbing, and per-message chat state.
    'skip_tables' => [
        'audit_logs', 'migrations', 'sessions', 'cache', 'cache_locks', 'jobs', 'job_batches', 'failed_jobs',
        'notifications', 'password_reset_tokens', 'password_resets', 'personal_access_tokens',
        'whatsapp_messages', 'whatsapp_sessions', 'invoice_sequences',
        // Bulk-import staging tables (thousands of rows). What they create in the real tables is still logged.
        'customerapplicationimports', 'customercdpimports', 'customerimports', 'customerprofessionimports',
        'customerregistrationimports', 'customeruserimports', 'professionimports',
    ],
    'skip_prefixes' => ['telescope_', 'pulse_'],

    // Columns whose values are never written to the log (a change is still noted, the value is hidden).
    'masked_columns' => ['password', 'remember_token', 'code_hash', 'secret', 'token', 'api_key', 'apikey'],

    // Changes to only these columns are not worth a log line.
    'ignored_columns' => ['updated_at', 'remember_token'],
];
