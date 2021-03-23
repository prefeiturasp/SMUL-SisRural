<?php

namespace App\Models\Traits;

use App\Helpers\General\AppHelper;
use Carbon\Carbon;

/**
 * Trait utilizado para formatar datas dos seguintes atributos:
 *
 * a) created_at
 * b) updated_at
 * c) expired_at
 * d) prazo
 *
 */
trait DateFormat
{
    public function getCreatedAtFormattedAttribute()
    {
        return AppHelper::formatDate($this->created_at);
    }

    public function getUpdatedAtFormattedAttribute()
    {
        return AppHelper::formatDate($this->updated_at);
    }

    public function getExpiredAtFormattedAttribute()
    {
        if ($this->expired_at)
            return Carbon::parse($this->expired_at)->format(('d/m/Y'));
        else
            return null;
    }

    public function getPrazoFormattedAttribute()
    {
        if ($this->prazo)
            return Carbon::parse($this->prazo)->format(('d/m/Y'));
        else
            return null;
    }

    public function getFinishedAtFormattedAttribute()
    {
        return $this->finished_at ? AppHelper::formatDate($this->finished_at) : null;
    }
}
