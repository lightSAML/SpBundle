<?php

namespace LightSaml\SpBundle\Security\Authentication\Provider;

use LightSaml\SpBundle\Security\Authentication\Token\SamlSpToken;
use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class LightsSamlSpAuthenticationProvider implements AuthenticationProviderInterface
{
    /** @var string */
    private $providerKey;

    /**
     * @param $providerKey
     */
    public function __construct($providerKey)
    {
        $this->providerKey = $providerKey;
    }

    /**
     * Attempts to authenticate a TokenInterface object.
     *
     * @param TokenInterface $token The TokenInterface instance to authenticate
     *
     * @return TokenInterface An authenticated TokenInterface instance, never null
     *
     * @throws AuthenticationException if the authentication fails
     */
    public function authenticate(TokenInterface $token)
    {
        if (false === $this->supports($token)) {
            return null;
        }

        return new SamlSpToken(['ROLE_USER'], $this->providerKey);
    }

    /**
     * Checks whether this provider supports the given token.
     *
     * @param TokenInterface $token A TokenInterface instance
     *
     * @return bool true if the implementation supports the Token, false otherwise
     */
    public function supports(TokenInterface $token)
    {
        return $token instanceof SamlSpToken;
    }
}
