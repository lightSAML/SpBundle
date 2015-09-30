<?php

namespace LightSaml\SpBundle\Bridge\Container;

use LightSaml\Build\Container\OwnContainerInterface;
use LightSaml\Credential\CredentialInterface;
use LightSaml\Provider\EntityDescriptor\EntityDescriptorProviderInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class OwnContainer extends AbstractContainer implements OwnContainerInterface
{
    /**
     * @return EntityDescriptorProviderInterface
     */
    public function getOwnEntityDescriptorProvider()
    {
        return $this->container->get('light_saml_sp.own.entity_descriptor_provider');
    }

    /**
     * @return CredentialInterface[]
     */
    public function getOwnCredentials()
    {
        return $this->container->get('light_saml_sp.own.credential_store')->getByEntityId(
            $this->container->getParameter('light_saml_sp.own.entity_id')
        );
    }
}
