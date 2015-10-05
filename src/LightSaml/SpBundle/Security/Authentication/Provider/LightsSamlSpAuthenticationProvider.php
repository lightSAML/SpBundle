<?php

namespace LightSaml\SpBundle\Security\Authentication\Provider;

use LightSaml\SpBundle\Security\Authentication\Token\SamlSpToken;
use LightSaml\SpBundle\Security\Authentication\Token\SamlSpResponseToken;
use LightSaml\SpBundle\Security\User\UserManagerInterface;
use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserChecker;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class LightsSamlSpAuthenticationProvider implements AuthenticationProviderInterface
{
    /** @var string */
    private $providerKey;

    /** @var UserProviderInterface */
    private $userProvider;

    /** @var bool */
    private $createIfNotExists;

    /** @var UserChecker */
    private $userChecker;

    /**
     * @param                       $providerKey
     * @param UserProviderInterface $userProvider
     * @param bool                  $createIfNotExists
     * @param UserChecker           $userChecker
     */
    public function __construct(
        $providerKey,
        UserProviderInterface $userProvider,
        $createIfNotExists = false,
        UserChecker $userChecker = null
    ) {
        $this->providerKey = $providerKey;
        $this->userProvider = $userProvider;
        $this->createIfNotExists = $createIfNotExists;
        $this->userChecker = $userChecker;
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

        /** @var SamlSpResponseToken $token */
        $result = new SamlSpToken(['ROLE_USER'], $this->providerKey);

        if ($token->getResponse()->getFirstAssertion() &&
            $token->getResponse()->getFirstAssertion()->getSubject() &&
            $token->getResponse()->getFirstAssertion()->getSubject()->getNameID() &&
            $token->getResponse()->getFirstAssertion()->getSubject()->getNameID()->getValue()
        ) {
            $result->setUser($token->getResponse()->getFirstAssertion()->getSubject()->getNameID()->getValue());
        }

        return $result;
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
        return $token instanceof SamlSpResponseToken;
    }

    private function getUser(SamlSpResponseToken $token)
    {
        if ($this->userProvider instanceof UserManagerInterface) {
            return $this->getUserFromManager($token);
        }

        return $this->getUserFromProvider($token);
    }

    /**
     * @param SamlSpResponseToken $token
     *
     * @return UserInterface
     */
    private function getUserFromManager(SamlSpResponseToken $token)
    {
        $user = null;
        /** @var UserManagerInterface $userManager */
        $userManager = $this->userProvider;
        try {
            $user = $userManager->loadUserByResponse($token->getResponse());
        } catch (UsernameNotFoundException $ex) {
            if (false == $this->createIfNotExists) {
                throw $ex;
            }
            $user = $userManager->createUserByResponse($token->getResponse());
        }

        if (false == $user instanceof UserInterface) {
            throw new \RuntimeException('User provider did not return an implementation of user interface.');
        }

        return $user;
    }

    /**
     * @param TokenInterface $token
     *
     * @return UserInterface
     */
    private function getUserFromProvider(TokenInterface $token)
    {
        // use mapper to map response to the username
        throw new UsernameNotFoundException('Not implemented');
    }
}
