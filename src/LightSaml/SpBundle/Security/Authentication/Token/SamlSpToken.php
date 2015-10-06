<?php

namespace LightSaml\SpBundle\Security\Authentication\Token;

use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;
use Symfony\Component\Security\Core\User\UserInterface;

class SamlSpToken extends AbstractToken
{
    /** @var string */
    private $providerKey;

    /**
     * @param array         $roles
     * @param string        $providerKey
     * @param array         $attributes
     * @param string|object $user
     */
    public function __construct(array $roles, $providerKey, array $attributes, $user)
    {
        parent::__construct($roles);

        $this->providerKey = $providerKey;
        $this->setAttributes($attributes);
        if ($user) {
            $this->setUser($user);
        }

        $this->setAuthenticated(true);
    }

    /**
     * Returns the user credentials.
     *
     * @return mixed The user credentials
     */
    public function getCredentials()
    {
        return '';
    }

    /**
     * @return string
     */
    public function getProviderKey()
    {
        return $this->providerKey;
    }
}
