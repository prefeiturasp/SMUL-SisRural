<?php

namespace App\Models\Core\Traits;

trait ImportFillableCreatedAt
{
    protected function initializeImportFillableCreatedAt()
    {
        if (\Config::get('import_service')) {
            $this->fillable[] = 'created_at';
            $this->fillable[] = 'updated_at';
        }
    }
}
