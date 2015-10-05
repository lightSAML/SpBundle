<?php

namespace LightSaml\SpBundle\Security\User;

use LightSaml\Model\Protocol\Response;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

interface UserManagerInterface extends UserProviderInterface
{
    /**
     * @param Response $response
     *
     * @return UserInterface
     *
     * @throws UsernameNotFoundException if the user is not found
     */
    public function loadUserByResponse(Response $response);

    /**
     * @param Response $response
     *
     * @return UserInterface
     */
    public function createUserByResponse(Response $response);
}
