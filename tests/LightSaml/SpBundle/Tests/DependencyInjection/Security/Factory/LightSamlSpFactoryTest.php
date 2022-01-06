<?php

namespace LightSaml\SpBundle\Tests\DependencyInjection\Security\Factory;

use LightSaml\SpBundle\DependencyInjection\Security\Factory\LightSamlSpFactory;
use Symfony\Component\Config\Definition\BooleanNode;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ScalarNode;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\HttpKernel\Kernel;

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
            ['force', BooleanNode::class, true],
            ['username_mapper', ScalarNode::class, 'lightsaml_sp.username_mapper.simple'],
            ['user_creator', ScalarNode::class, null],
            ['attribute_mapper', ScalarNode::class, 'lightsaml_sp.attribute_mapper.simple'],
            ['token_factory', ScalarNode::class, 'lightsaml_sp.token_factory'],
        ];
    }

    /**
     * @dataProvider configuration_provider
     */
    public function test_configuration($configurationName, $type, $defaultValue)
    {
        $factory = new LightSamlSpFactory();
        
		if (Kernel::VERSION_ID >= 40200) {
            $treeBuilder = new TreeBuilder('name');
            $root = $treeBuilder->getRootNode();
        } else {
            $treeBuilder = new TreeBuilder();
            $root = $treeBuilder->root('name');
        }
		
		$factory->addConfiguration($root);
		
		$children = $treeBuilder->buildTree()->getChildren();
        $this->assertArrayHasKey($configurationName, $children);
        $this->assertInstanceOf($type, $children['force']);

        $this->assertEquals($defaultValue, $children[$configurationName]->getDefaultValue());
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
        $containerBuilder = new ContainerBuilder(new ParameterBag());
        $config = $this->getDefaultConfig();
        $factory = new LightSamlSpFactory();
        list(, , $entryPointId) = $factory->create($containerBuilder, 'main', $config, 'user.provider.id', $defaultEntryPoint = null);
        $this->assertStringStartsWith('security.authentication.form_entry_point', $entryPointId);
        $this->assertStringEndsWith('.main', $entryPointId);
    }

    // TODO test values injected on provider in function createAuthProvider()

    public function test_creates_auth_provider_service()
    {
        $containerBuilder = new ContainerBuilder(new ParameterBag());
        $config = $this->getDefaultConfig();
        $factory = new LightSamlSpFactory();
        list($providerId) = $factory->create($containerBuilder, 'main', $config, 'user.provider.id', $defaultEntryPoint = null);
        $this->assertTrue($containerBuilder->hasDefinition($providerId));
    }

    public function test_injects_user_provider_to_auth_provider()
    {
        $containerBuilder = new ContainerBuilder(new ParameterBag());
        $config = $this->getDefaultConfig();
        $factory = new LightSamlSpFactory();
        list($providerId) = $factory->create($containerBuilder, 'main', $config, $userProvider = 'user.provider.id', $defaultEntryPoint = null);
        $definition = $containerBuilder->getDefinition($providerId);
        /** @var \Symfony\Component\DependencyInjection\Reference $reference */
        $reference = $definition->getArgument(1);
        $this->assertInstanceOf(\Symfony\Component\DependencyInjection\Reference::class, $reference);
        $this->assertEquals($userProvider, (string) $reference);
    }

    public function test_injects_username_mapper_to_auth_provider()
    {
        $containerBuilder = new ContainerBuilder(new ParameterBag());
        $config = $this->getDefaultConfig();
        $factory = new LightSamlSpFactory();
        list($providerId) = $factory->create($containerBuilder, 'main', $config, $userProvider = 'user.provider.id', $defaultEntryPoint = null);
        $definition = $containerBuilder->getDefinition($providerId);
        /** @var \Symfony\Component\DependencyInjection\Reference $reference */
        $reference = $definition->getArgument(4);
        $this->assertInstanceOf(\Symfony\Component\DependencyInjection\Reference::class, $reference);
        $this->assertEquals($config['username_mapper'], (string) $reference);
    }

    public function test_injects_user_creator_to_auth_provider()
    {
        $containerBuilder = new ContainerBuilder(new ParameterBag());
        $config = $this->getDefaultConfig();
        $factory = new LightSamlSpFactory();
        list($providerId) = $factory->create($containerBuilder, 'main', $config, $userProvider = 'user.provider.id', $defaultEntryPoint = null);
        $definition = $containerBuilder->getDefinition($providerId);
        /** @var \Symfony\Component\DependencyInjection\Reference $reference */
        $reference = $definition->getArgument(5);
        $this->assertInstanceOf(\Symfony\Component\DependencyInjection\Reference::class, $reference);
        $this->assertEquals($config['user_creator'], (string) $reference);
    }

    public function test_injects_attribute_mapper_to_auth_provider()
    {
        $containerBuilder = new ContainerBuilder(new ParameterBag());
        $config = $this->getDefaultConfig();
        $factory = new LightSamlSpFactory();
        list($providerId) = $factory->create($containerBuilder, 'main', $config, $userProvider = 'user.provider.id', $defaultEntryPoint = null);
        $definition = $containerBuilder->getDefinition($providerId);
        /** @var \Symfony\Component\DependencyInjection\Reference $reference */
        $reference = $definition->getArgument(6);
        $this->assertInstanceOf(\Symfony\Component\DependencyInjection\Reference::class, $reference);
        $this->assertEquals($config['attribute_mapper'], (string) $reference);
    }

    public function test_injects_token_factory_to_auth_provider()
    {
        $containerBuilder = new ContainerBuilder(new ParameterBag());
        $config = $this->getDefaultConfig();
        $factory = new LightSamlSpFactory();
        list($providerId) = $factory->create($containerBuilder, 'main', $config, $userProvider = 'user.provider.id', $defaultEntryPoint = null);
        $definition = $containerBuilder->getDefinition($providerId);
        /** @var \Symfony\Component\DependencyInjection\Reference $reference */
        $reference = $definition->getArgument(7);
        $this->assertInstanceOf(\Symfony\Component\DependencyInjection\Reference::class, $reference);
        $this->assertEquals($config['token_factory'], (string) $reference);
    }

    /**
     * @return array
     */
    private function getDefaultConfig()
    {
        return [
            'force' => true,
            'username_mapper' => 'lightsaml_sp.username_mapper.simple',
            'token_factory' => 'lightsaml_sp.token_factory',
            'user_creator' => 'some.user.creator',
            'attribute_mapper' => 'some.attribute.mapper',
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
