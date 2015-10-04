<?php

namespace LightSaml\SpBundle\Security\Authentication\Token;

use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;

class SamlSpToken extends AbstractToken
{
    /** @var string */
    private $providerKey;

    /**
     * @param array  $roles
     * @param string $providerKey
     */
    public function __construct(array $roles = array(), $providerKey)
    {
        parent::__construct($roles);

        $this->providerKey = $providerKey;

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
