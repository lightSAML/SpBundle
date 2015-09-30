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

    public function testSetsOwnEntityDescriptorFactoryFromEntityDescriptorFile()
    {
        $containerBuilder = new ContainerBuilder(new ParameterBag());
        $extension = new LightSamlSpExtension();
        $config = $this->getDefaultConfig();

        $extension->load($config, $containerBuilder);

        $this->assertTrue($containerBuilder->hasDefinition('light_saml_sp.own.entity_descriptor'));
        $definition = $containerBuilder->getDefinition('light_saml_sp.own.entity_descriptor');
        $this->assertEquals(
            ['LightSaml\Provider\EntityDescriptor\FileEntityDescriptorProviderFactory', 'fromEntityDescriptorFile'],
            $definition->getFactory()
        );
        $this->assertCount(1, $definition->getArguments());
        $this->assertEquals($config['light_saml_sp']['own']['entity_descriptor']['filename'], $definition->getArgument(0));
    }

    public function testSetsOwnEntityDescriptorToCustomFactory()
    {
        $containerBuilder = new ContainerBuilder(new ParameterBag());
        $extension = new LightSamlSpExtension();
        $config = $this->getDefaultConfig();
        $config['light_saml_sp']['own']['entity_descriptor']['factory'] = $expectedFactory = 'some.factory';

        $extension->load($config, $containerBuilder);

        $this->assertTrue($containerBuilder->hasDefinition('light_saml_sp.own.entity_descriptor'));
        $definition = $containerBuilder->getDefinition('light_saml_sp.own.entity_descriptor');
        $factory = $definition->getFactory();
        $this->assertInstanceOf('Symfony\Component\DependencyInjection\Reference', $factory[0]);
        $this->assertEquals($expectedFactory, (string)$factory[0]);
        $this->assertEquals('get', $factory[1]);
        $this->assertCount(0, $definition->getArguments());
    }

    public function testSetsOwnCredentialFactoryToFiles()
    {
        $containerBuilder = new ContainerBuilder(new ParameterBag());
        $extension = new LightSamlSpExtension();
        $config = $this->getDefaultConfig();

        $extension->load($config, $containerBuilder);

        $this->assertTrue($containerBuilder->hasDefinition('light_saml_sp.own.credential'));
        $definition = $containerBuilder->getDefinition('light_saml_sp.own.credential');
        $factory = $definition->getFactory();
        $this->assertInstanceOf('Symfony\Component\DependencyInjection\Reference', $factory[0]);
        $this->assertEquals('light_saml_sp.own.credential.factory.file', (string)$factory[0]);
        $this->assertEquals('get', $factory[1]);

        $this->assertTrue($containerBuilder->hasDefinition('light_saml_sp.own.credential.factory.file'));
        $definition = $containerBuilder->getDefinition('light_saml_sp.own.credential.factory.file');
        $this->assertCount(4, $definition->getArguments());
        $this->assertEquals('%light_saml_sp.own.entity_id%', $definition->getArgument(0));
        $this->assertEquals($config['light_saml_sp']['own']['credential']['certificate'], $definition->getArgument(1));
        $this->assertEquals($config['light_saml_sp']['own']['credential']['private_key'], $definition->getArgument(2));
        $this->assertEquals($config['light_saml_sp']['own']['credential']['password'], $definition->getArgument(3));
    }

    public function testSetsOwnCredentialToCustomFactory()
    {
        $containerBuilder = new ContainerBuilder(new ParameterBag());
        $extension = new LightSamlSpExtension();
        $config = $this->getDefaultConfig();
        $config['light_saml_sp']['own']['credential']['factory'] = $expectedFactory = 'some.factory';

        $extension->load($config, $containerBuilder);

        $this->assertTrue($containerBuilder->hasDefinition('light_saml_sp.own.credential'));
        $definition = $containerBuilder->getDefinition('light_saml_sp.own.credential');
        $factory = $definition->getFactory();
        $this->assertInstanceOf('Symfony\Component\DependencyInjection\Reference', $factory[0]);
        $this->assertEquals($expectedFactory, (string)$factory[0]);
        $this->assertEquals('get', $factory[1]);
        $this->assertCount(0, $definition->getArguments());
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
                    'entity_descriptor' => [
                        'filename' => 'entity_descriptor.xml',
                    ],
                    'credential' => [
                        'certificate' => 'saml.crt',
                        'private_key' => 'saml.pem',
                        'password' => '123',
                    ],
                ]
            ]
        ];
    }
}
