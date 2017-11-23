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

use LightSaml\SpBundle\Infrastructure\RestApi;

class HttpApi extends RestApi implements Api
{
    public function getMetadata($organisation)
    {
        return $this->sendRequest('GET', sprintf('organisation/%s/metadata', $organisation))->getBody()->getContents();
    }
}
