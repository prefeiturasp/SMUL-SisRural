<?php

namespace App\Models\Auth\Traits\Method;

/**
 * Trait UserMethod.
 */
trait UserMethod
{
    /**
     * @return bool
     */
    public function canChangeEmail()
    {
        return config('access.users.change_email');
    }

    /**
     * @return bool
     */
    public function canChangePassword()
    {
        return !app('session')->has(config('access.socialite_session_name'));
    }

    /**
     * @param bool $size
     *
     * @throws \Illuminate\Container\EntryNotFoundException
     * @return bool|\Illuminate\Contracts\Routing\UrlGenerator|mixed|string
     */
    public function getPicture($size = false)
    {
        switch ($this->avatar_type) {
            case 'gravatar':
                if (!$size) {
                    $size = config('gravatar.default.size');
                }

                return gravatar()->get($this->email, ['size' => $size]);

            case 'storage':
                return url('storage/' . $this->avatar_location);
        }

        $social_avatar = $this->providers()->where('provider', $this->avatar_type)->first();

        if ($social_avatar && strlen($social_avatar->avatar)) {
            return $social_avatar->avatar;
        }

        return false;
    }

    /**
     * @param $provider
     *
     * @return bool
     */
    public function hasProvider($provider)
    {
        foreach ($this->providers as $p) {
            if ($p->provider == $provider) {
                return true;
            }
        }

        return false;
    }

    /**
     * Usuário do tipo Admin (é o administrador do sistema)
     *
     * Libera visualização de tudo.
     *
     * @return bool
     */
    public function isAdminLOP()
    {
        return $this->hasRole(config('access.users.app_admin_role'));
    }

    /**
     * Usuário do tipo Domínio
     *
     * @return bool
     */
    public function isDominio()
    {
        return $this->hasRole(config('access.users.domain_role'));
    }

    /**
     * Usuário do tipo Unidade Operacional
     *
     * @return bool
     */
    public function isUnidOperacional()
    {
        return $this->hasRole(config('access.users.operational_unit_role'));
    }

    /**
     * Usuário do tipo Técnico
     *
     * @return bool
     */
    public function isTecnico()
    {
        return $this->hasRole(config('access.users.technician_role'));
    }

    /**
     * Usuário do tipo Técnico Externo
     *
     * //TODO não existe ainda uma implementação para esse tipo de usuário, apenas foi criado a referência
     *
     * @return bool
     */
    public function isTecnicoExterno()
    {
        return $this->hasRole(config('access.users.admin_role'));
    }

    /**
     * Usuário do tipo "Super Administrador"
     *
     * Ninguém tem acesso a esse usuário, é um usuário "root".
     *
     * Ele não deve ser utilizado p/ cadastros.
     *
     * @return mixed
     */
    public function isAdmin()
    {
        return $this->hasRole(config('access.users.admin_role'));
    }

    /**
     * Retorna se o usuário esta ativo (ou não).
     *
     * Usuários podem ser desativados por usuários "pais" ou "irmãos"
     *
     * @return bool
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @return bool
     */
    public function isConfirmed()
    {
        return $this->confirmed;
    }

    /**
     * @return bool
     */
    public function isPending()
    {
        return config('access.users.requires_approval') && !$this->confirmed;
    }
}
