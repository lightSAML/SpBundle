<?php

namespace LightSaml\SpBundle\Bridge\Container;

use LightSaml\Build\Container\StoreContainerInterface;
use LightSaml\Store\Id\IdStoreInterface;
use LightSaml\Store\Request\RequestStateStoreInterface;
use LightSaml\Store\Sso\SsoStateStoreInterface;

class StoreContainer extends AbstractContainer implements StoreContainerInterface
{
    /**
     * @return RequestStateStoreInterface
     */
    public function getRequestStateStore()
    {
        return $this->container->get('light_saml_sp.store.request');
    }

    /**
     * @return IdStoreInterface
     */
    public function getIdStateStore()
    {
        return $this->container->get('light_saml_sp.store.id_state');
    }

    /**
     * @return SsoStateStoreInterface
     */
    public function getSsoStateStore()
    {
        return $this->container->get('light_saml_sp.store.sso_state');
    }
}
