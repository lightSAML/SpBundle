<?php
/*
 * This file is part of the LightSAML SP-Bundle package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace LightSaml\SpBundle\Tests\DependencyInjection\Compiler;

use LightSaml\SpBundle\DependencyInjection\Compiler\InjectIdpDataMetadataProviderCompilerPass;
use LightSaml\SpBundle\DependencyInjection\Configuration;
use LightSaml\SpBundle\Store\Credential\CompositeCredentialStore;
use LightSaml\SpBundle\Store\EntityDescriptor\CompositeEntityDescriptorStore;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class InjectIdpDataMetadataProviderCompilerPassTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itUseDefaultMetadataProviderIfDisabled()
    {
        $compiler = new InjectIdpDataMetadataProviderCompilerPass();
        $compiler->process(new ContainerBuilder());
    }

    /** @test */
    public function itUseIdpDataMetadataProviderIfEnabled()
    {
        $config = [
            'enabled' => true,
            'idp_data_url' => 'idp-data',
            'domain_resolver_url' => 'domain-resolver'
        ];

        $container = new ContainerBuilder();
        $container->setDefinition(
            'lightsaml.party.idp_entity_descriptor_store',
            new Definition(CompositeEntityDescriptorStore::class)
        );
        $container->setDefinition(
            'lightsaml.own.credential_store',
            new Definition(CompositeCredentialStore::class)
        );

        foreach ($config as $parameter => $value) {
            $container->setParameter(
                sprintf(
                    '%s.%s.%s',
                    Configuration::CONFIGURATION_NAME,
                    Configuration::IDP_DATA_METADATA_PROVIDER,
                    $parameter
                ),
                $value
            );
        }

        $compiler = new InjectIdpDataMetadataProviderCompilerPass();
        $compiler->process($container);
    }
}
