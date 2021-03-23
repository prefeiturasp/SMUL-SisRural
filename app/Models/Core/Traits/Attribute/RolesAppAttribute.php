<?php

namespace App\Models\Core\Traits\Attribute;

trait RolesAppAttribute
{
    /**
     * Determina se o usuário pode visualizar ou não o registro
     *
     * Regra utilizada pelo APP, no CMS é desabilitado essa função
     *
     * @return bool
     */
    public function getCanViewAttribute()
    {
        if (!\Config::get('app_sync')) {
            return;
        }

        return \Gate::allows('view', $this);
    }

    /**
     * Determina se o usuário pode atualizar ou não o registro
     *
     * Regra utilizada pelo APP, no CMS é desabilitado essa função
     *
     * @return bool
     */
    public function getCanUpdateAttribute()
    {
        if (!\Config::get('app_sync')) {
            return;
        }

        return \Gate::allows('update', $this);
    }

    /**
     * Determina se o usuário pode remover ou não o registro
     *
     * Regra utilizada pelo APP, no CMS é desabilitado essa função
     *
     * @return bool
     */
    public function getCanDeleteAttribute()
    {
        if (!\Config::get('app_sync')) {
            return;
        }

        return \Gate::allows('delete', $this);
    }
}
