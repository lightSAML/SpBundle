<?php

namespace LightSaml\SpBundle\Tests\DependencyInjection;

use LightSaml\SpBundle\DependencyInjection\LightSamlSpExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

class LightSamlSpExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function testLoadsWithConfiguration()
    {
        $containerBuilder = new ContainerBuilder(new ParameterBag());
        $extension = new LightSamlSpExtension();
        $config = $this->getDefaultConfig();

        $extension->load($config, $containerBuilder);
    }

    public function testLoadsBuildContainer()
    {
        $containerBuilder = new ContainerBuilder(new ParameterBag());
        $extension = new LightSamlSpExtension();
        $config = $this->getDefaultConfig();
        $extension->load($config, $containerBuilder);

        $this->assertTrue($containerBuilder->hasDefinition('light_saml_sp.container.build'));
        $this->assertEquals('LightSaml\SpBundle\Bridge\Container\BuildContainer', $containerBuilder->getDefinition('light_saml_sp.container.build')->getClass());
    }

    public function testSetsOwnEntityDescriptorProviderFactoryFromEntityDescriptorFile()
    {
        $containerBuilder = new ContainerBuilder(new ParameterBag());
        $extension = new LightSamlSpExtension();
        $config = $this->getDefaultConfig();

        $extension->load($config, $containerBuilder);

        $this->assertTrue($containerBuilder->hasDefinition('light_saml_sp.own.entity_descriptor_provider'));
        $definition = $containerBuilder->getDefinition('light_saml_sp.own.entity_descriptor_provider');
        $this->assertEquals(
            ['LightSaml\Provider\EntityDescriptor\FileEntityDescriptorProviderFactory', 'fromEntityDescriptorFile'],
            $definition->getFactory()
        );
        $this->assertCount(1, $definition->getArguments());
        $this->assertEquals($config['light_saml_sp']['own']['entity_descriptor_provider']['filename'], $definition->getArgument(0));
    }

    public function testSetsOwnEntityDescriptorProviderToCustomAlias()
    {
        $containerBuilder = new ContainerBuilder(new ParameterBag());
        $extension = new LightSamlSpExtension();
        $config = $this->getDefaultConfig();
        $config['light_saml_sp']['own']['entity_descriptor_provider']['id'] = $expectedAlias = 'some.factory';

        $extension->load($config, $containerBuilder);

        $this->assertTrue($containerBuilder->hasAlias('light_saml_sp.own.entity_descriptor_provider'));
        $this->assertEquals($expectedAlias, (string)$containerBuilder->getAlias('light_saml_sp.own.entity_descriptor_provider'));
    }

    public function testLoadsOwnCredentialStore()
    {
        $containerBuilder = new ContainerBuilder(new ParameterBag());
        $extension = new LightSamlSpExtension();
        $config = $this->getDefaultConfig();

        $extension->load($config, $containerBuilder);

        $this->assertTrue($containerBuilder->hasDefinition('light_saml_sp.own.credential_store'));
        $definition = $containerBuilder->getDefinition('light_saml_sp.own.credential_store');
        $this->assertEquals('LightSaml\Store\Credential\StaticCredentialStore', $definition->getClass());
    }

    public function testLoadsSystemTimeProvider()
    {
        $containerBuilder = new ContainerBuilder(new ParameterBag());
        $extension = new LightSamlSpExtension();
        $config = $this->getDefaultConfig();

        $extension->load($config, $containerBuilder);

        $this->assertTrue($containerBuilder->hasDefinition('light_saml_sp.system.time_provider'));
    }

    public function testLoadsSystemEventDispatcher()
    {
        $containerBuilder = new ContainerBuilder(new ParameterBag());
        $extension = new LightSamlSpExtension();
        $config = $this->getDefaultConfig();

        $extension->load($config, $containerBuilder);

        $this->assertTrue($containerBuilder->hasDefinition('light_saml_sp.system.event_dispatcher'));
    }

    public function testLoadsSystemCustomEventDispatcher()
    {
        $containerBuilder = new ContainerBuilder(new ParameterBag());
        $extension = new LightSamlSpExtension();
        $config = $this->getDefaultConfig();
        $config['light_saml_sp']['system']['event_dispatcher'] = $expectedAlias = 'some.service';

        $extension->load($config, $containerBuilder);

        $this->assertTrue($containerBuilder->hasAlias('light_saml_sp.system.event_dispatcher'));
        $this->assertEquals($expectedAlias, (string)$containerBuilder->getAlias('light_saml_sp.system.event_dispatcher'));
    }

    public function testLoadsSystemLogger()
    {
        $containerBuilder = new ContainerBuilder(new ParameterBag());
        $extension = new LightSamlSpExtension();
        $config = $this->getDefaultConfig();

        $extension->load($config, $containerBuilder);

        $this->assertTrue($containerBuilder->hasAlias('light_saml_sp.system.logger'));
    }

    public function testLoadsSystemCustomLogger()
    {
        $containerBuilder = new ContainerBuilder(new ParameterBag());
        $extension = new LightSamlSpExtension();
        $config = $this->getDefaultConfig();
        $config['light_saml_sp']['system']['logger'] = $expectedAlias = 'some.service';

        $extension->load($config, $containerBuilder);

        $this->assertTrue($containerBuilder->hasAlias('light_saml_sp.system.logger'));
        $this->assertEquals($expectedAlias, (string)$containerBuilder->getAlias('light_saml_sp.system.logger'));
    }

    private function getDefaultConfig()
    {
        return [
            'light_saml_sp' => [
                'own' => [
                    'entity_descriptor_provider' => [
                        'filename' => 'entity_descriptor.xml',
                    ],
                ]
            ]
        ];
    }
}
