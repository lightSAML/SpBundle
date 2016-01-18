<?php

/*
 * This file is part of the LightSAML SP-Bundle package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\SpBundle\Security\Authentication\Token;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class SamlSpTokenFactory implements SamlSpTokenFactoryInterface
{
    /**
     * @param string              $providerKey
     * @param array               $attributes
     * @param mixed               $user
     * @param SamlSpResponseToken $responseToken
     *
     * @return TokenInterface
     */
    public function create($providerKey, array $attributes, $user, SamlSpResponseToken $responseToken)
    {
        $token = new SamlSpToken(
            $user instanceof UserInterface ? $user->getRoles() : [],
            $providerKey,
            $attributes,
            $user
        );

        return $token;
    }
}
