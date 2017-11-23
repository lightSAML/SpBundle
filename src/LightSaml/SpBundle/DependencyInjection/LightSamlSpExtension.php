<?php

/*
 * This file is part of the LightSAML SP-Bundle package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\SpBundle\DependencyInjection;

use LightSaml\SpBundle\Security\Authentication\EntityId\EntityIdProviderInterface as Provider;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

class LightSamlSpExtension extends Extension
{
    /**
     * Loads a specific configuration.
     *
     * @param array            $config    An array of configuration values
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
        $loader->load('security.yml');
        $loader->load('services.yml');

        $this->configureSimpleUsernameMapper($config, $container);
        $this->configureEntityIdProvider($config, $container);
        $this->configureIdpDataMetadataProvider($config, $container);
    }

    private function configureSimpleUsernameMapper(array $config, ContainerBuilder $container)
    {
        $definition = $container->getDefinition('lightsaml_sp.username_mapper.simple');
        $definition->replaceArgument(0, $config['username_mapper']);
    }

    private function configureEntityIdProvider(array $config, ContainerBuilder $container)
    {
        if (isset($config[Provider::PROVIDER_NAME])) {
            $container->setParameter(
                sprintf('%s.%s', Configuration::CONFIGURATION_NAME, Provider::PROVIDER_NAME),
                $config[Provider::PROVIDER_NAME]
            );
        }
    }

    private function configureIdpDataMetadataProvider(array $config, ContainerBuilder $container)
    {
        if (isset($config[Configuration::IDP_DATA_METADATA_PROVIDER])) {
            $params = ['enabled', 'idp_data_url', 'domain_resolver_url'];
            foreach ($params as $param) {
                $container->setParameter(
                    sprintf(
                        '%s.%s.%s',
                        Configuration::CONFIGURATION_NAME,
                        Configuration::IDP_DATA_METADATA_PROVIDER,
                        $param
                    ),
                    $config[Configuration::IDP_DATA_METADATA_PROVIDER][$param]
                );
            }
        }
    }
}
