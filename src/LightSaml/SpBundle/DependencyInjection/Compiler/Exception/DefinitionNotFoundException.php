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

class DefinitionNotFoundException extends \Exception
{
    public function __construct($name)
    {
        parent::__construct(
            sprintf('Definition %s not found', $name),
            500
        );
    }
}
