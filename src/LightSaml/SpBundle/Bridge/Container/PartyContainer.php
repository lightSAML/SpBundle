<?php

namespace LightSaml\SpBundle\Bridge\Container;

use LightSaml\Build\Container\PartyContainerInterface;
use LightSaml\Store\EntityDescriptor\EntityDescriptorStoreInterface;
use LightSaml\Store\TrustOptions\TrustOptionsStoreInterface;

class PartyContainer extends AbstractContainer implements PartyContainerInterface
{
    /**
     * @return EntityDescriptorStoreInterface
     */
    public function getIdpEntityDescriptorStore()
    {
        return $this->container->get('light_saml_sp.party.idp_entity_descriptor_store');
    }

    /**
     * @return EntityDescriptorStoreInterface
     */
    public function getSpEntityDescriptorStore()
    {
        return $this->container->get('light_saml_sp.party.sp_entity_descriptor_store');
    }

    /**
     * @return TrustOptionsStoreInterface
     */
    public function getTrustOptionsStore()
    {
        return $this->container->get('light_saml_sp.party.trust_options_store');
    }
}
