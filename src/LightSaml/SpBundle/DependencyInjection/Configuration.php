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

use LightSaml\ClaimTypes;
use LightSaml\SpBundle\Security\User\SimpleUsernameMapper;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use LightSaml\SpBundle\Security\Authentication\EntityId\EntityIdProviderInterface as Provider;

class Configuration implements ConfigurationInterface
{
    const CONFIGURATION_NAME = 'light_saml_sp';
    const IDP_DATA_METADATA_PROVIDER = 'idp_data_metadata_provider';

    /**
     * Generates the configuration tree builder.
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $root = $treeBuilder->root(self::CONFIGURATION_NAME);

        $root
            ->children()
                ->arrayNode('username_mapper')
                    ->defaultValue([
                        ClaimTypes::EMAIL_ADDRESS,
                        ClaimTypes::ADFS_1_EMAIL,
                        ClaimTypes::COMMON_NAME,
                        ClaimTypes::WINDOWS_ACCOUNT_NAME,
                        'urn:oid:0.9.2342.19200300.100.1.3',
                        'uid',
                        'urn:oid:1.3.6.1.4.1.5923.1.1.1.6',
                        SimpleUsernameMapper::NAME_ID,
                    ])
                    ->prototype('scalar')->end()
                ->end()
                ->scalarNode(Provider::PROVIDER_NAME)->end()
                ->arrayNode(self::IDP_DATA_METADATA_PROVIDER)
                    ->children()
                        ->scalarNode('enabled')
                            ->defaultValue(0)
                        ->end()
                        ->scalarNode('idp_data_url')
                            ->isRequired()
                            ->cannotBeEmpty()
                        ->end()
                        ->scalarNode('domain_resolver_url')
                            ->isRequired()
                            ->cannotBeEmpty()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
