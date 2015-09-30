<?php

namespace LightSaml\SpBundle\Bridge\Container;

use LightSaml\Build\Container\SystemContainerInterface;
use LightSaml\Provider\TimeProvider\TimeProviderInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class SystemContainer extends AbstractContainer implements SystemContainerInterface
{
    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->container->get('request_stack')->getCurrentRequest();
    }

    /**
     * @return SessionInterface
     */
    public function getSession()
    {
        return $this->container->get('session');
    }

    /**
     * @return TimeProviderInterface
     */
    public function getTimeProvider()
    {
        return $this->container->get('light_saml_sp.system.time_provider');
    }

    /**
     * @return EventDispatcherInterface
     */
    public function getEventDispatcher()
    {
        return $this->container->get('light_saml_sp.system.event_dispatcher');
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger()
    {
        return $this->container->get('light_saml_sp.system.logger');
    }
}
