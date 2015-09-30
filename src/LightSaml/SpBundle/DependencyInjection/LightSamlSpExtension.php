<?php

namespace LightSaml\SpBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

class LightSamlSpExtension extends Extension
{
    /**
     * Loads a specific configuration.
     *
     * @param array            $config An array of configuration values
     * @param ContainerBuilder $container A ContainerBuilder instance
     *
     * @throws \InvalidArgumentException When provided tag is not defined in this extension
     *
     * @api
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $config);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('container.yml');
        $loader->load('system.yml');
        $loader->load('own.yml');

        $this->configureOwn($container, $config);
        $this->configureSystem($container, $config);
    }

    private function configureOwn(ContainerBuilder $container, array $config)
    {
        $this->configureOwnEntityDescriptor($container, $config);
    }

    private function configureOwnEntityDescriptor(ContainerBuilder $container, array $config)
    {
        if (isset($config['own']['entity_descriptor_provider']['id'])) {
            $container->setAlias('light_saml_sp.own.entity_descriptor_provider', $config['own']['entity_descriptor_provider']['id']);
        } elseif (isset($config['own']['entity_descriptor_provider']['filename'])) {
            $definition = $container->getDefinition('light_saml_sp.own.entity_descriptor_provider');
            if (isset($config['own']['entity_descriptor_provider']['entity_id'])) {
                $definition->setFactory(['LightSaml\Provider\EntityDescriptor\FileEntityDescriptorProviderFactory', 'fromEntitiesDescriptorFile']);
                $definition->addArgument($config['own']['entity_descriptor_provider']['filename']);
                $definition->addArgument($config['own']['entity_descriptor_provider']['entity_id']);
            } else {
                $definition->setFactory(['LightSaml\Provider\EntityDescriptor\FileEntityDescriptorProviderFactory', 'fromEntityDescriptorFile']);
                $definition->addArgument($config['own']['entity_descriptor_provider']['filename']);
            }
        } else {
            throw new InvalidConfigurationException('light_saml.own.entity_descriptor must have either factory or filename configuration option');
        }
    }

    private function configureSystem(ContainerBuilder $container, array $config)
    {
        if (isset($config['system']['event_dispatcher'])) {
            $container->removeDefinition('light_saml_sp.system.event_dispatcher');
            $container->setAlias('light_saml_sp.system.event_dispatcher', $config['system']['event_dispatcher']);
        }

        if (isset($config['system']['logger'])) {
            $container->setAlias('light_saml_sp.system.logger', $config['system']['logger']);
        }
    }
}
