<?php

namespace App\Auth;

use App\Models\Auth\User;
use Laravel\Passport\Bridge\AccessToken as PassportAccessToken;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use League\OAuth2\Server\CryptKey;

class CustomAccessToken extends PassportAccessToken
{
    private $privateKey;

    /**
     * @param  mixed $privateKey
     * @return void
     */
    public function convertToJWT(CryptKey $privateKey)
    {
        $builder = new Builder();
        $builder->permittedFor($this->getClient()->getIdentifier())
            ->identifiedBy($this->getIdentifier(), true)
            ->issuedAt(time())
            ->canOnlyBeUsedAfter(time())
            ->expiresAt($this->getExpiryDateTime()->getTimestamp())
            ->relatedTo($this->getUserIdentifier())
            ->withClaim('scopes', $this->getScopes());

        /**
         * Os dados liberados no "withClaim" poderão ser consumidos pelo APP. Irá dentro do JWT Token
         */
        if ($user = User::find($this->getUserIdentifier())) {
            $builder
                ->withClaim('user', $user->only('id', 'first_name', 'last_name', 'document'));

            $builder->withClaim('user.role', $user->roles->first()->only('id', 'name'));
            $builder->withClaim('user.dominio', $user->singleDominio()->only('id', 'nome'));
        }
        return $builder
            ->getToken(new Sha256(), new Key($privateKey->getKeyPath(), $privateKey->getPassPhrase()));
    }

    public function __toString()
    {
        return (string)$this->convertToJWT($this->privateKey);
    }

    /**
     * Set the private key used to encrypt this access token.
     */
    public function setPrivateKey(CryptKey $privateKey)
    {
        $this->privateKey = $privateKey;
    }
}
