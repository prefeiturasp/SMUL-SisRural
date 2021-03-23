<?php

namespace App\Auth;

use Laravel\Passport\Bridge\AccessTokenRepository as PassportAccessTokenRepository;
use League\OAuth2\Server\Entities\ClientEntityInterface;

// AccessToken from step 1

class AccessTokenRepository extends PassportAccessTokenRepository
{
    public function getNewToken(ClientEntityInterface $clientEntity, array $scopes, $userIdentifier = null)
    {
        return new CustomAccessToken($userIdentifier, $scopes, $clientEntity); // AccessToken from step 1
    }
}
