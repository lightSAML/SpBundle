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

interface SamlSpTokenInterface
{

    /**
     * Returns the user credentials.
     *
     * @return mixed The user credentials
     */
    public function getCredentials();

    /**
     * @return string
     */
    public function getProviderKey();
}
