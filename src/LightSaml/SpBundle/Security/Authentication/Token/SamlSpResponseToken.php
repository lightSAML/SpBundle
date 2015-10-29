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

use LightSaml\Model\Protocol\Response;

class SamlSpResponseToken extends SamlSpToken
{
    /** @var Response */
    private $response;

    public function __construct(Response $response, $providerKey)
    {
        parent::__construct([], $providerKey, [], null);

        $this->response = $response;

        $this->setAuthenticated(false);
    }

    /**
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }
}
