<?php

/*
 * This file is part of the LightSAML SP-Bundle package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\SpBundle\Security\User;

use LightSaml\SpBundle\Security\Authentication\Token\SamlSpResponseToken;

interface AttributeMapperInterface
{
    /**
     * @param SamlSpResponseToken $token
     *
     * @return array
     */
    public function getAttributes(SamlSpResponseToken $token);
}
