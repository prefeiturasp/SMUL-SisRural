<?php

namespace App\Helpers\General;

class CacheHelper
{
    /**
     * Otimização p/ sync mobile
     *
     * Retorna o domínio do usuário
     */
    public static function singleDominio($user)
    {
        return \Cache::store('array')->remember("UnidadeOperacionalPermissionScope-singleDominio-{$user->id}", 60, function () use ($user) {
            return $user->singleDominio()->id;
        });
    }

    public static function singleDominioRow($user)
    {
        return \Cache::store('array')->remember("UnidadeOperacionalPermissionScope-singleDominio-{$user->id}", 60, function () use ($user) {
            return $user->singleDominio();
        });
    }
}
