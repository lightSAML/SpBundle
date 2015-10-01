<?php

namespace LightSaml\SpBundle;

use LightSaml\SpBundle\DependencyInjection\Compiler\AddMethodCallCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class LightSamlSpBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new AddMethodCallCompilerPass(
            'light_saml_sp.own.credential_store',
            'lightsaml.own_credential_store',
            'add'
        ));
        $container->addCompilerPass(new AddMethodCallCompilerPass(
            'light_saml_sp.party.trust_options_store',
            'lightsaml.trust_options_store',
            'add'
        ));
        $container->addCompilerPass(new AddMethodCallCompilerPass(
            'light_saml_sp.party.idp_entity_descriptor_store',
            'lightsaml.idp_entity_store',
            'add'
        ));
        $container->addCompilerPass(new AddMethodCallCompilerPass(
            'light_saml_sp.credential.credential_store_factory',
            'lightsaml.credential',
            'addExtraCredential'
        ));
    }
}
