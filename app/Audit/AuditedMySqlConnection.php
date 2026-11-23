<?php

namespace App\Audit;

use Illuminate\Database\MySqlConnection;

class AuditedMySqlConnection extends MySqlConnection
{
    use AuditsQueries;
}
