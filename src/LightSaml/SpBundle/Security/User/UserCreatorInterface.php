<?php

namespace LightSaml\SpBundle\Security\User;

use LightSaml\Model\Protocol\Response;
use Symfony\Component\Security\Core\User\UserInterface;

interface UserCreatorInterface
{
    /**
     * @param Response $response
     *
     * @return UserInterface|null
     */
    public function createUser(Response $response);
}
