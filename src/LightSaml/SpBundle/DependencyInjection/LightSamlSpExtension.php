<?php

namespace LightSaml\SpBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Definition;
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
        $loader->load('own.yml');
        $loader->load('system.yml');
        $loader->load('party.yml');
        $loader->load('store.yml');
        $loader->load('credential.yml');
        $loader->load('service.yml');
        $loader->load('profile.yml');

        $this->configureOwn($container, $config);
        $this->configureSystem($container, $config);
        $this->configureStore($container, $config);
    }

    private function configureOwn(ContainerBuilder $container, array $config)
    {
        $container->setParameter('light_saml_sp.own.entity_id', $config['own']['entity_id']);

        $this->configureOwnEntityDescriptor($container, $config);
        $this->configureOwnCredentials($container, $config);
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
        }
    }

    private function configureOwnCredentials(ContainerBuilder $container, array $config)
    {
        if (false == isset($config['own']['credentials'])) {
            return;
        }

        foreach ($config['own']['credentials'] as $id=>$data) {
            $definition = new Definition(
                'LightSaml\Store\Credential\X509FileCredentialStore',
                [
                    $config['own']['entity_id'],
                    $data['certificate'],
                    $data['key'],
                    $data['password']
                ]
            );
            $definition->addTag('lightsaml.own_credential_store');
            $container->setDefinition('light_saml_sp.own.credential_store.'.$id, $definition);
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

    private function configureStore(ContainerBuilder $container, array $config)
    {
        if (isset($config['store']['request'])) {
            $container->setAlias('light_saml_sp.store.request', $config['store']['request']);
        }
        if (isset($config['store']['id_state'])) {
            $container->setAlias('light_saml_sp.store.id_state', $config['store']['id_state']);
        }
        if (isset($config['store']['sso_state'])) {
            $container->setAlias('light_saml_sp.store.sso_state', $config['store']['sso_state']);
        }
    }
}
