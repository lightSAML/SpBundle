<?php

/*
 * This file is part of the LightSAML SP-Bundle package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\SpBundle\Security\Authentication\Provider;

use LightSaml\ClaimTypes;
use LightSaml\SamlConstants;
use LightSaml\SpBundle\Security\Authentication\Token\SamlSpResponseToken;
use LightSaml\SpBundle\Security\Authentication\Token\SamlSpToken;
use LightSaml\SpBundle\Security\Authentication\Token\SamlSpTokenFactoryInterface;
use LightSaml\SpBundle\Security\User\AttributeMapperInterface;
use LightSaml\SpBundle\Security\User\UserCreatorInterface;
use LightSaml\SpBundle\Security\User\UsernameMapperInterface;
use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class LightsSamlSpAuthenticationProvider implements AuthenticationProviderInterface
{
    /** @var string */
    private $providerKey;

    /** @var UserProviderInterface */
    private $userProvider;

    /** @var bool */
    private $force;

    /** @var UserCheckerInterface|null */
    private $userChecker;

    /** @var UsernameMapperInterface|null */
    private $usernameMapper;

    /** @var UserCreatorInterface */
    private $userCreator;

    /** @var AttributeMapperInterface */
    private $attributeMapper;

    /** @var SamlSpTokenFactoryInterface */
    private $tokenFactory;

    /**
     * @param string                           $providerKey
     * @param UserProviderInterface|null       $userProvider
     * @param bool                             $force
     * @param UserCheckerInterface|null        $userChecker
     * @param UsernameMapperInterface|null     $usernameMapper
     * @param UserCreatorInterface|null        $userCreator
     * @param AttributeMapperInterface|null    $attributeMapper
     * @param SamlSpTokenFactoryInterface|null $tokenFactory
     */
    public function __construct(
        $providerKey,
        UserProviderInterface $userProvider = null,
        $force = false,
        UserCheckerInterface $userChecker = null,
        UsernameMapperInterface $usernameMapper = null,
        UserCreatorInterface $userCreator = null,
        AttributeMapperInterface $attributeMapper = null,
        SamlSpTokenFactoryInterface $tokenFactory = null
    ) {
        $this->providerKey = $providerKey;
        $this->userProvider = $userProvider;
        $this->force = $force;
        $this->userChecker = $userChecker;
        $this->usernameMapper = $usernameMapper;
        $this->userCreator = $userCreator;
        $this->attributeMapper = $attributeMapper;
        $this->tokenFactory = $tokenFactory;
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
            throw new \LogicException('Unsupported token');
        }

        /* @var SamlSpResponseToken $token */

        $user = null;
        try {
            $user = $this->loadUser($token);
        } catch (UsernameNotFoundException $ex) {
            $user = $this->createUser($token);
        }

        if (null === $user && $this->force) {
            $user = $this->createDefaultUser($token);
        }

        if (null === $user) {
            $ex = new AuthenticationException('Unable to resolve user');
            $ex->setToken($token);

            throw $ex;
        }

        if ($this->userChecker && $user instanceof UserInterface) {
            $this->userChecker->checkPreAuth($user);
            $this->userChecker->checkPostAuth($user);
        }

        $attributes = $this->getAttributes($token);

        if ($this->tokenFactory) {
            $result = $this->tokenFactory->create(
                $this->providerKey,
                $attributes,
                $user,
                $token
            );
        } else {
            $result = new SamlSpToken(
                $user instanceof UserInterface ? $user->getRoles() : [],
                $this->providerKey,
                $attributes,
                $user
            );
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

    /**
     * @param SamlSpResponseToken $token
     *
     * @return UserInterface
     *
     * @throws UsernameNotFoundException
     */
    private function loadUser(SamlSpResponseToken $token)
    {
        if (null === $this->usernameMapper || null === $this->userProvider) {
            throw new UsernameNotFoundException();
        }

        $username = $this->usernameMapper->getUsername($token->getResponse());

        $user = $this->userProvider->loadUserByUsername($username);

        if (false === $user instanceof UserInterface) {
            throw new \LogicException('User provider must return instance of UserInterface');
        }

        return $user;
    }

    /**
     * @param SamlSpResponseToken $token
     *
     * @return null|UserInterface
     */
    private function createUser(SamlSpResponseToken $token)
    {
        if (null === $this->userCreator) {
            return null;
        }

        $user = $this->userCreator->createUser($token->getResponse());

        if ($user && false === $user instanceof UserInterface) {
            throw new \LogicException('User creator must return instance of UserInterface or null');
        }

        return $user;
    }

    /**
     * @param SamlSpResponseToken $token
     *
     * @return null|string
     */
    private function createDefaultUser(SamlSpResponseToken $token)
    {
        if ($token->getResponse()->getFirstAssertion() &&
            $token->getResponse()->getFirstAssertion()->getSubject() &&
            $token->getResponse()->getFirstAssertion()->getSubject()->getNameID() &&
            $token->getResponse()->getFirstAssertion()->getSubject()->getNameID()->getFormat() != SamlConstants::NAME_ID_FORMAT_TRANSIENT &&
            $token->getResponse()->getFirstAssertion()->getSubject()->getNameID()->getValue()
        ) {
            return $token->getResponse()->getFirstAssertion()->getSubject()->getNameID()->getValue();
        }

        if ($token->getResponse()->getFirstAssertion() &&
            $attributeStatement = $token->getResponse()->getFirstAssertion()->getFirstAttributeStatement()
        ) {
            $names = [
                ClaimTypes::COMMON_NAME,
                ClaimTypes::EMAIL_ADDRESS,
                ClaimTypes::WINDOWS_ACCOUNT_NAME,
                ClaimTypes::ADFS_1_EMAIL,
                ClaimTypes::UPN,
                ClaimTypes::ADFS_1_UPN,
            ];

            foreach ($names as $name) {
                $attribute = $attributeStatement->getFirstAttributeByName($name);
                if ($attribute && $attribute->getFirstAttributeValue()) {
                    return $attribute->getFirstAttributeValue();
                }
            }
        }

        return null;
    }

    /**
     * @param SamlSpResponseToken $token
     *
     * @return array
     */
    private function getAttributes(SamlSpResponseToken $token)
    {
        if (null === $this->attributeMapper) {
            return [];
        }

        $attributes = $this->attributeMapper->getAttributes($token);

        if (false === is_array($attributes)) {
            throw new \LogicException('Attribute mapper must return array');
        }

        return $attributes;
    }
}
