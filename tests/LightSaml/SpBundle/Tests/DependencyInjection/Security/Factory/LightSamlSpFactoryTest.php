<?php

namespace LightSaml\SpBundle\Tests\DependencyInjection\Security\Factory;

use LightSaml\SpBundle\DependencyInjection\Security\Factory\LightSamlSpFactory;
use Symfony\Component\Config\Definition\BooleanNode;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ScalarNode;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

class LightSamlSpFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function test_constructs_without_arguments()
    {
        new LightSamlSpFactory();
    }

    public function test_key()
    {
        $factory = new LightSamlSpFactory();
        $this->assertEquals('light_saml_sp', $factory->getKey());
    }

    public function test_position()
    {
        $factory = new LightSamlSpFactory();
        $this->assertEquals('form', $factory->getPosition());
    }

    public function configuration_provider()
    {
        return [
            ['force', BooleanNode::class, false],
            ['username_mapper', ScalarNode::class, 'lightsaml_sp.username_mapper.simple'],
            ['user_creator', ScalarNode::class, null],
            ['attribute_mapper', ScalarNode::class, null],
        ];
    }

    /**
     * @dataProvider configuration_provider
     */
    public function test_configuration($configurationName, $type, $defaultValue)
    {
        $factory = new LightSamlSpFactory();
        $treeBuilder = new TreeBuilder();
        $factory->addConfiguration($treeBuilder->root('name'));
        $childeren = $treeBuilder->buildTree()->getChildren();
        $this->assertArrayHasKey($configurationName, $childeren);
        $this->assertInstanceOf($type, $childeren['force']);

        $this->assertEquals($defaultValue, $childeren[$configurationName]->getDefaultValue());
    }

    public function test_create_returns_array_with_provider_listener_and_entry_point()
    {
        $containerBuilder = new ContainerBuilder(new ParameterBag());
        $config = $this->getDefaultConfig();
        $factory = new LightSamlSpFactory();
        $result = $factory->create($containerBuilder, 'main', $config, 'user.provider.id', $defaultEntryPoint = null);
        $this->assertInternalType('array', $result);
        $this->assertCount(3, $result);
        $this->assertContainsOnly('string', $result);
    }

    public function test_returns_lightsaml_provider_with_sufix()
    {
        $containerBuilder = new ContainerBuilder(new ParameterBag());
        $config = $this->getDefaultConfig();
        $factory = new LightSamlSpFactory();
        list($providerId) = $factory->create($containerBuilder, 'main', $config, 'user.provider.id', $defaultEntryPoint = null);
        $this->assertStringStartsWith('security.authentication.provider.lightsaml_sp', $providerId);
        $this->assertStringEndsWith('.main', $providerId);
    }

    public function test_returns_lightsaml_listener_with_sufix()
    {
        $containerBuilder = new ContainerBuilder(new ParameterBag());
        $config = $this->getDefaultConfig();
        $factory = new LightSamlSpFactory();
        list(, $listenerId) = $factory->create($containerBuilder, 'main', $config, 'user.provider.id', $defaultEntryPoint = null);
        $this->assertStringStartsWith('security.authentication.listener.lightsaml_sp', $listenerId);
        $this->assertStringEndsWith('.main', $listenerId);
    }

    public function test_returns_entry_point()
    {
        // TODO reconsider entry point
    }

    // TODO test values injected on provider in function createAuthProvider()

    /**
     * @return array
     */
    private function getDefaultConfig()
    {
        return [
            'force' => false,
            'username_mapper' => 'lightsaml_sp.username_mapper.simple',
            'user_creator' => null,
            'attribute_mapper' => null,
            'remember_me' => true,
            'provider' => 'some.provider',
            'success_handler' => 'success_handler',
            'failure_handler' => 'failure_handler',
            'check_path' => '/login_check',
            'use_forward' => false,
            'require_previous_session' => true,
            'always_use_default_target_path' => false,
            'default_target_path' => '/',
            'login_path' => '/login',
            'target_path_parameter' => '_target_path',
            'use_referer' => false,
            'failure_path' => null,
            'failure_forward' => false,
            'failure_path_parameter' => '_failure_path',
        ];
    }
}
