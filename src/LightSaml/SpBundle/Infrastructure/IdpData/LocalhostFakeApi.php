<?php
/*
 * This file is part of the LightSAML SP-Bundle package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace LightSaml\SpBundle\Infrastructure\IdpData;

class LocalhostFakeApi implements Api
{
    public function getMetadata($organisation)
    {
        return file_get_contents(
            __DIR__ . '/../../../../../vendor/lightsaml/lightsaml/web/sp/localhost-lightsaml-lightsaml-idp.xml'
        );
    }
}
