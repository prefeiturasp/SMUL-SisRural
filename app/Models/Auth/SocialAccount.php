<?php

namespace App\Models\Auth;

use App\Models\RecordingModel;

/**
 * Class SocialAccount.
 *
 * @deprecated Futuramente deve ser removido do projeto, não temos implementação de "social".
 */
class SocialAccount extends RecordingModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'social_accounts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'provider',
        'provider_id',
        'token',
        'avatar',
    ];
}
