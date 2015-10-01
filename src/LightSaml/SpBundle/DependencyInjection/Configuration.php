<?php

namespace LightSaml\SpBundle\DependencyInjection;

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

        $root->children()
            ->arrayNode('own')
                ->children()
                    ->scalarNode('entity_id')->isRequired()->cannotBeEmpty()->end()
                    ->arrayNode('entity_descriptor_provider')
                        ->children()
                            ->scalarNode('id')->end()
                            ->scalarNode('filename')->end()
                            ->scalarNode('entity_id')->end()
                        ->end()
                    ->end()
                    ->arrayNode('credentials')
                        ->prototype('array')
                            ->children()
                                ->scalarNode('certificate')->end()
                                ->scalarNode('key')->end()
                                ->scalarNode('password')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
            ->arrayNode('system')
                ->children()
                    ->scalarNode('event_dispatcher')->defaultValue(null)->end()
                    ->scalarNode('logger')->defaultValue(null)->end()
                ->end()
            ->end()
            ->arrayNode('store')
                ->children()
                    ->scalarNode('request')->end()
                    ->scalarNode('id_state')->end()
                    ->scalarNode('sso_state')->end()
                ->end()
            ->end()
        ->end();

        return $treeBuilder;
    }
}
