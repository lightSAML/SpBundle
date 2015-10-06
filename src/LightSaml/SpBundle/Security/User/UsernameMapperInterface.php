<?php

namespace LightSaml\SpBundle\Security\User;

use LightSaml\Model\Protocol\Response;

interface UsernameMapperInterface
{
    /**
     * @param Response $response
     *
     * @return string|null
     */
    public function getUsername(Response $response);
}
