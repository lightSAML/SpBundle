<?php

/*
 * This file is part of the LightSAML SP-Bundle package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\SpBundle\DependencyInjection\Compiler\Exception;

use LightSaml\SpBundle\Security\Authentication\EntityId\EntityIdProviderInterface;

class InvalidEntityIdProviderException extends \Exception
{
    public function __construct($providerName)
    {
        parent::__construct(
            sprintf('Class %s must implement %s', $providerName, EntityIdProviderInterface::class),
            500
        );
    }
}
