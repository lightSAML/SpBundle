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
        $this->configureOwnCredential($container, $config);
    }

    private function configureOwnEntityDescriptor(ContainerBuilder $container, array $config)
    {
        $definition = $container->getDefinition('light_saml_sp.own.entity_descriptor');
        if (isset($config['own']['entity_descriptor']['factory'])) {
            $definition->setFactory([new Reference($config['own']['entity_descriptor']['factory']), 'get']);
        } elseif (isset($config['own']['entity_descriptor']['filename'])) {
            if (isset($config['own']['entity_descriptor']['entity_id'])) {
                $definition->setFactory(['LightSaml\Provider\EntityDescriptor\FileEntityDescriptorProviderFactory', 'fromEntitiesDescriptorFile']);
                $definition->addArgument($config['own']['entity_descriptor']['filename']);
                $definition->addArgument($config['own']['entity_descriptor']['entity_id']);
            } else {
                $definition->setFactory(['LightSaml\Provider\EntityDescriptor\FileEntityDescriptorProviderFactory', 'fromEntityDescriptorFile']);
                $definition->addArgument($config['own']['entity_descriptor']['filename']);
            }
        } else {
            throw new InvalidConfigurationException('light_saml.own.entity_descriptor must have either factory or filename configuration option');
        }
    }

    private function configureOwnCredential(ContainerBuilder $container, array $config)
    {
        $definition = $container->getDefinition('light_saml_sp.own.credential');
        if (isset($config['own']['credential']['factory'])) {
            $definition->setFactory([new Reference($config['own']['credential']['factory']), 'get']);
        } else {
            $factory = $container->getDefinition('light_saml_sp.own.credential.factory.file');
            $factory->replaceArgument(1, $config['own']['credential']['certificate']);
            $factory->replaceArgument(2, $config['own']['credential']['private_key']);
            $factory->replaceArgument(3, $config['own']['credential']['password']);
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
