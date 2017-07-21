<?php

/*
 * This file is part of the LightSAML SP-Bundle package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\SpBundle\Security\Authentication\EntityId\Exception;

class NoRequestAvailableException extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct('Request stack did not return any Request object', 500);
    }
}
