<?php

/*
 * This file is part of the LightSAML SP-Bundle package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\SpBundle\Security\Authentication\EntityId;

interface EntityIdProviderInterface
{
    const PROVIDER_NAME = 'entity_id_provider';
    const PROVIDER_PATTERN = "service('%s').getEntityId()";

    /**
     * @return string
     */
    public function getEntityId();
}
