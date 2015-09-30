<?php

namespace LightSaml\SpBundle\Bridge\Container;

use LightSaml\Build\Container\BuildContainerInterface;
use LightSaml\Build\Container\CredentialContainerInterface;
use LightSaml\Build\Container\OwnContainerInterface;
use LightSaml\Build\Container\PartyContainerInterface;
use LightSaml\Build\Container\ProviderContainerInterface;
use LightSaml\Build\Container\ServiceContainerInterface;
use LightSaml\Build\Container\StoreContainerInterface;
use LightSaml\Build\Container\SystemContainerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class BuildContainer extends AbstractContainer implements BuildContainerInterface
{
    /** @var SystemContainer */
    private $systemContainer;

    /**
     * @return SystemContainerInterface
     */
    public function getSystemContainer()
    {
        if (null == $this->systemContainer) {
            $this->systemContainer = new SystemContainer($this->container);
        }

        return $this->systemContainer;
    }

    /**
     * @return PartyContainerInterface
     */
    public function getPartyContainer()
    {
        // TODO: Implement getPartyContainer() method.
    }

    /**
     * @return StoreContainerInterface
     */
    public function getStoreContainer()
    {
        // TODO: Implement getStoreContainer() method.
    }

    /**
     * @return ProviderContainerInterface
     */
    public function getProviderContainer()
    {
        // TODO: Implement getProviderContainer() method.
    }

    /**
     * @return CredentialContainerInterface
     */
    public function getCredentialContainer()
    {
        // TODO: Implement getCredentialContainer() method.
    }

    /**
     * @return ServiceContainerInterface
     */
    public function getServiceContainer()
    {
        // TODO: Implement getServiceContainer() method.
    }

    /**
     * @return OwnContainerInterface
     */
    public function getOwnContainer()
    {
        // TODO: Implement getOwnContainer() method.
    }
}
