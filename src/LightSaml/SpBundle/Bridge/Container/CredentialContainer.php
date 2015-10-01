<?php

namespace LightSaml\SpBundle\Bridge\Container;

use LightSaml\Build\Container\CredentialContainerInterface;
use LightSaml\Store\Credential\CredentialStoreInterface;

class CredentialContainer extends AbstractContainer implements CredentialContainerInterface
{
    /**
     * @return CredentialStoreInterface
     */
    public function getCredentialStore()
    {
        return $this->container->get('light_saml_sp.credential.credential_store');
    }
}
