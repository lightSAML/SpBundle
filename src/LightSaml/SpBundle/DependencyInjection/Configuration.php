<?php

namespace LightSaml\SpBundle\DependencyInjection;

use LightSaml\ClaimTypes;
use LightSaml\SpBundle\Security\User\SimpleUsernameMapper;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree builder.
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $root = $treeBuilder->root('light_saml_sp');

        $root
            ->children()
                ->arrayNode('username_mapper')
                    ->defaultValue([
                        ClaimTypes::COMMON_NAME,
                        ClaimTypes::EMAIL_ADDRESS,
                        ClaimTypes::ADFS_1_EMAIL,
                        ClaimTypes::WINDOWS_ACCOUNT_NAME,
                        SimpleUsernameMapper::NAME_ID,
                    ])
                    ->prototype('scalar')->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
