<?php
/*
 * This file is part of the LightSAML SP-Bundle package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace LightSaml\SpBundle\DependencyInjection\Compiler;

use LightSaml\SpBundle\Store\Credential\CompositeCredentialStore;
use LightSaml\SpBundle\Store\EntityDescriptor\CompositeEntityDescriptorStore;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class InjectIdpDataMetadataProviderCompilerPass implements CompilerPassInterface
{
    const METADATA_PROVIDER_SWITCH = 'light_saml_sp.idp_data_metadata_provider.enabled';

    public function process(ContainerBuilder $container)
    {
        if ($this->isIdpDataMetadataProviderEnabled($container)) {
            $injectEntityDescriptor = $container->getDefinition('lightsaml.party.idp_entity_descriptor_store');
            $injectEntityDescriptor->setClass(CompositeEntityDescriptorStore::class);
            $injectEntityDescriptor->addMethodCall('removeAll');
            $injectEntityDescriptor
                ->addMethodCall('add', [new Reference('lightsaml.party.idp_entity_descriptor_store.idp_data')]);

            $injectCredentialStore = $container->getDefinition('lightsaml.own.credential_store');
            $injectCredentialStore->setClass(CompositeCredentialStore::class);
            $injectCredentialStore->addMethodCall('removeAll');
            $injectCredentialStore
                ->addMethodCall('add', [new Reference('lightsaml.lightsaml.own_credential_store.idp_data')]);
        }
    }

    /**
     * @param ContainerBuilder $container
     * @return bool
     */
    private function isIdpDataMetadataProviderEnabled(ContainerBuilder $container)
    {
        return
            $container->hasParameter(self::METADATA_PROVIDER_SWITCH) &&
            $container->getParameter(self::METADATA_PROVIDER_SWITCH);
    }
}
