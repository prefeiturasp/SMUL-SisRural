<?php

namespace App\Models\Core\Traits\Scope;

use App\Helpers\General\AppHelper;
use App\Helpers\General\CacheHelper;
use Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class ChecklistPermissionScope implements Scope
{
    /**
     * Libera os templates de checklist de acordo com o usuário logado.
     *
     * Quando um "Domínio" cria um template, ele determina quem pode "aplicar" esse template.
     *
     * A aplicação pode ser liberada para "domínios", "unidades operacionais" ou "usuários"
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        if (session('auth_user_id') || Auth::user()) {
            $user = AppHelper::getSessionOrAuthUser();

            if ($user->isAdmin() || $user->isAdminLOP()) return;

            $dominio_id = CacheHelper::singleDominio($user);

            /**
             * Permissão de aplicação do tipo "Domínio"
             * Lógica -> whereHas('dominiosScoped'). Não foi utilizado o "dominiosScoped" p/ otimizar a busca (ganho de 300ms)
             */
            $builder->whereHas('dominios', function ($q) use ($dominio_id) {
                $q->where('dominios.id', $dominio_id);
            });

            //Permissão de aplicação do tipo "Unidades Operacionais", só as Unid. Operacionais que o usuário tem permissão para "ver"
            $builder->orWhereHas('unidadesOperacionaisScoped');

            //Permissão de aplicação do tipo "Usuário", não precisa ser "scoped", porque pega exatamente o usuário que esta logado
            $userId = $user->id;
            $builder->orWhereHas('usuarios', function ($q) use ($userId) {
                $q->where('users.id', $userId);
            });

            //Dono do Checklist, Usuario tipo DOMINIO visualiza o Checklist, ele precisa enxergar o que "criou" para poder editar.
            if ($user->can('view same domain farmers')) {
                $builder->orWhere('dominio_id', $dominio_id);
            }
        }
    }
}
