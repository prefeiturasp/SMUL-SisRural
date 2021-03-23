<?php

namespace App\Models\Auth;

use App\Helpers\General\CacheHelper;
use App\Models\Auth\Traits\Attribute\UserAttribute;
use App\Models\Auth\Traits\Method\UserMethod;
use App\Models\Auth\Traits\Relationship\UserRelationship;
use App\Models\Auth\Traits\Scope\UserPermissionScope;
use App\Models\Auth\Traits\Scope\UserScope;
use App\Models\Core\DominioModel;
use App\Models\Core\ProdutorModel;
use App\Models\Core\Traits\Scope\DominioPermissionScope;
use App\Models\Core\Traits\Scope\UnidadeOperacionalPermissionScope;
use App\Models\Core\UnidadeOperacionalModel;

/**
 * Class User.
 *
 * Um usuário pode possuir N domínios, N unidades operacionais e N roles.
 *
 * As caracteristicas de NEGÓCIO fizeram com que partissemos para a seguinte solução:
 *
 * Um registro de "usuário" só possuí UMA "role", UM "domínio" e N "unidades operacionais que estão dentro do "domínio" do usuário.
 *
 * Para atender a regra de multiplas "roles", "dominios" e "unidades operacionais" foi implementado uma regra de "multiplos usuários" com mesmo CPF / E-MAIL.
 *
 * Todas alterações de dados do usuário é replicado para os outros "ids" de usuários com mesmo "cpf"/"email".
 *
 * CPF é sempre atrelado a um "único email"
 *
 * E-mail é sempre atrelado a um "único cpf".
 *
 * Alterações de senha, alteram em todos os usuários com mesmo "cpf/email".
 *
 * Criação de usuários, adicionado vários tratamentos p/ não permitir utilização de um CPF ou E-MAIL já cadastrado no sistema.
 *
 * Edição de usuários, adicionado vários tratamentos p/ não permitir que um usuário altere o CPF (ou E-MAIL) para algum já utilizado no sistema.
 *
 * ------
 *
 * No final o sistema fica transparente para o usuário. Ele enxerga um usuário com multiplas roles.
 *
 * No momento do login o usuário pode selecionar qual "domínio/role" ele quer logar.
 *
 * No topo do sistema (após logado) também é possível fazer essa alteração.
 *
 * Nas partes de liberação de usuário, como, permitir que um usuário aplique um caderno de campo, é mostrado uma lista de Usuários formatado com (Nome do domínio, Role, nome do usuário) p/ o ID do usuário ser selecionado corretamente.
 *
 */
class User extends BaseUser
{
    use UserAttribute,
        UserMethod,
        UserRelationship,
        UserScope;

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new UserPermissionScope);
    }

    /**
     * Retorna todos os domínios do usuário (com o refatoramento do sistema, o usuário só pode possuir UM domínio)
     *
     * Domínios "escopados", significa que só vai mostrar na listagem os domínios que o "Usuário logado" tem permissão para enxergar
     *
     * Foi mantido o método porque ainda não foi refatorado
     *
     * @return mixed
     */
    public function dominios()
    {
        return $this->belongsToMany(DominioModel::class, 'user_dominios', 'user_id', 'dominio_id')->whereNull('user_dominios.deleted_at')->withPivot('id')->withTimestamps();
    }

    /**
     * Retorna todos os domínios do usuário, independente se este domínio é visível pelo usuário logado ou não.
     *
     * @return mixed
     */
    public function dominiosNS()
    {
        return $this->belongsToMany(DominioModel::class, 'user_dominios', 'user_id', 'dominio_id')->whereNull('user_dominios.deleted_at')->withPivot('id')->withTimestamps()->withoutGlobalScopes([DominioPermissionScope::class]);
    }

    /**
     * Retorna todas as unidades operacionais do usuário
     *
     * Unidades Operacionais "escopadas", significa que só vai mostrar na listagem as "unidades" que o "Usuário logado" tem permissão para enxergar
     *
     * @return mixed
     */
    public function unidadesOperacionais()
    {
        return $this->belongsToMany(UnidadeOperacionalModel::class, 'user_unidade_operacionais', 'user_id', 'unidade_operacional_id')->whereNull('user_unidade_operacionais.deleted_at')->withPivot('id')->withTimestamps();
    }

    /**
     * Retorna todas as unidades operacionais do usuário, independente se a "unidade operacional" é visível pelo usuário logado ou não.
     *
     * @return mixed
     */
    public function unidadesOperacionaisNS()
    {
        return $this->belongsToMany(UnidadeOperacionalModel::class, 'user_unidade_operacionais', 'user_id', 'unidade_operacional_id')->whereNull('user_unidade_operacionais.deleted_at')->withPivot('id')->withTimestamps()->withoutGlobalScopes([UnidadeOperacionalPermissionScope::class]);
    }

    /**
     * Retorna o Nome Completo do usuário + Nome do Dominio + Habilidade
     *
     * @return string
     */
    public function getFullNameDominioRoleAttribute()
    {
        $self = $this;

        return \Cache::store('file')->remember("getFullNameDominioRoleAttribute_{$this->id}", 3600, function () use ($self) {
            return $self->first_name . ' ' . $this->last_name . ' - ' . $self->singleDominio()->nome . ' - ' . \App\Enums\RolesEnum::toSelectArray()[$self->roles()->first()->name];
        });
    }

    /**
     * Retorna o Nome do Dominio + Habilidade
     *
     * @return string
     */
    public function getDominioRoleAttribute()
    {
        $self = $this;

        return \Cache::store('file')->remember("getDominioRoleAttribute{$this->id}", 3600, function () use ($self) {
            return $self->singleDominio()->nome . ' - ' . \App\Enums\RolesEnum::toSelectArray()[$self->roles()->first()->name];
        });
    }

    /**
     * Retorna todas as Roles que o CPF (document) do usuário possuí.
     *
     * O CPF pode ter N usuários, cada usuário pode ter sua respectiva role (domínio / unidade operacional / role)
     *
     * @return mixed
     */
    public function userRoles()
    {
        return User::withoutGlobalScopes()->where("document", $this->document)->get();
    }

    /**
     *
     * Método criado para refatorar métodos que retornavam N domínios de um usuário
     *
     * A estrutura do banco aceita que o usuário faça parte de N domínios.
     *
     * Mas a estrutura do projeto só permite que ele faça parte de UM domínio.
     *
     * Caso ele faça parte de mais um domínio, será criado um NOVO ID do usuário, com o mesmo CPF/EMAIL, apontando para o outro domínio.
     *
     * Máxima: Usuário só faz parte de UM DOMÍNIO
     *
     * @return App\Models\Core\DominioModel
     */
    public function singleDominio()
    {
        if ($this->isDominio()) {
            return $this->dominiosNS->first();
        } else {
            return $this->unidadesOperacionaisNS()->with('dominioNS')->get()->pluck('dominioNS')->first();
        }
    }

    /**
     * Retorna a lista de "roles" de um determinado cpf (document)
     *
     * É formatado para ficar legível qual domínio/role o usuário pertence.
     *
     * Formato: [Domínio] - [Nome da Role]
     *
     * @return array
     */
    public function allRoles()
    {
        $users = User::withoutGlobalScopes()
            ->withoutTrashed()
            ->where("document", $this->document)
            ->where('active', 1)
            ->where('confirmed', 1)
            ->with(['dominiosNS', 'unidadesOperacionaisNS', 'roles'])
            ->get();


        $return = array();
        foreach ($users as $k => $v) {
            if ($v->isAdmin()) {
                continue;
            }

            foreach ($v->roles as $kRole => $vRole) {
                $return[] = array("id" => $v->id, "nome" => @$v->singleDominio()->nome . ' - ' . \App\Enums\RolesEnum::toSelectArray()[$vRole->name]);
            }
        }

        $return = collect($return)->sortBy('nome')->values();

        return $return;
    }

    /**
     * UTILIZADO APENAS NO REPORT, NÃO UTILIZAR EM OUTRAS PARTES DO SISTEMA
     */

    public function produtores()
    {
        return $this->hasMany(ProdutorModel::class, 'user_id');
    }
}
