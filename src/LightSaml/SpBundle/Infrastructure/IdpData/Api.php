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

interface Api
{
    public function getMetadata($organisation);

    public function getCertificate($organisation);

    public function getPrivateKey($organisation);
}
