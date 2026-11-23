<?php

namespace App\Audit;

/**
 * Makes a database connection hand out the audited query builder.
 */
trait AuditsQueries
{
    public function query()
    {
        return new AuditedQueryBuilder($this, $this->getQueryGrammar(), $this->getPostProcessor());
    }
}
