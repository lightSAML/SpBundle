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

class SamlSpTokenFactory implements SamlSpTokenFactoryInterface
{

    /**
     * @param array $roles
     * @param string $providerKey
     * @param array $attributes
     * @param $user
     * @return mixed
     */
    public function create(array $roles, $providerKey, array $attributes, $user)
    {
        $token = new SamlSpToken(
            $roles,
            $providerKey,
            $attributes,
            $user
        );
        $token->setAttributes($attributes);
        $token->setAuthenticated(true);

        return $token;
    }
}
