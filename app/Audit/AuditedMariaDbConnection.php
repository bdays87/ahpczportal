<?php

namespace App\Audit;

use Illuminate\Database\MariaDbConnection;

class AuditedMariaDbConnection extends MariaDbConnection
{
    use AuditsQueries;
}
