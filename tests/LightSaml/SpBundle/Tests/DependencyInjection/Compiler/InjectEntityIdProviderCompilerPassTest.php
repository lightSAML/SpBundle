<?php

namespace LightSaml\SpBundle\Tests\DependencyInjection\Compiler;

use LightSaml\SpBundle\DependencyInjection\Compiler\InjectEntityIdProviderCompilerPass;
use LightSaml\SpBundle\DependencyInjection\Configuration;
use LightSaml\SpBundle\Security\Authentication\EntityId\EntityIdProviderInterface as Provider;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class InjectEntityIdProviderCompilerPassTest extends \PHPUnit_Framework_TestCase
{
    public function test_without_entity_id_provider()
    {
        $compiler = new InjectEntityIdProviderCompilerPass();
        $compiler->process(new ContainerBuilder());
    }

    public function test_valid_entity_id_provider()
    {
        $customProvider = 'custom_provider';
        $parameterName = sprintf('%s.%s', Configuration::CONFIGURATION_NAME, Provider::PROVIDER_NAME);

        $container = new ContainerBuilder();
        $container->setDefinition($customProvider, new Definition(ExampleProvider::class));
        $container->setParameter($parameterName, $customProvider);

        $compiler = new InjectEntityIdProviderCompilerPass();
        $this->setDefinitions($container, $compiler->getServiceArgumentMap());
        $compiler->process($container);
    }

    /**
     * @expectedException \LightSaml\SpBundle\DependencyInjection\Compiler\Exception\InvalidEntityIdProviderException
     */
    public function test_invalid_entity_id_provider()
    {
        $customProvider = 'custom_provider';
        $parameterName = sprintf('%s.%s', Configuration::CONFIGURATION_NAME, Provider::PROVIDER_NAME);

        $container = new ContainerBuilder();
        $container->setDefinition($customProvider, new Definition(Example::class));
        $container->setParameter($parameterName, $customProvider);

        $compiler = new InjectEntityIdProviderCompilerPass();
        $compiler->process($container);
    }

    /**
     * @expectedException \LightSaml\SpBundle\DependencyInjection\Compiler\Exception\DefinitionNotFoundException
     */
    public function test_invalid_service_map()
    {
        $customProvider = 'custom_provider';
        $parameterName = sprintf('%s.%s', Configuration::CONFIGURATION_NAME, Provider::PROVIDER_NAME);

        $container = new ContainerBuilder();
        $container->setDefinition($customProvider, new Definition(ExampleProvider::class));
        $container->setParameter($parameterName, $customProvider);

        $compiler = new InjectEntityIdProviderCompilerPass();
        $compiler->process($container);
    }

    private function setDefinitions(ContainerBuilder $container, array $map)
    {
        foreach ($map as $service => $argument) {
            $container->setDefinition($service, new Definition(Example::class, ['a', 'b', 'c', 'd']));
        }
    }
}

class ExampleProvider implements Provider
{
    public function getEntityId()
    {
        return '';
    }
}

class Example
{
}
