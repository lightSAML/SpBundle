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
                    ->arrayNode('entity_descriptor')
                        ->children()
                            ->scalarNode('factory')->end()
                            ->scalarNode('filename')->end()
                            ->scalarNode('entity_id')->end()
                        ->end()
                    ->end()
                    ->arrayNode('credential')
                        ->children()
                            ->scalarNode('factory')->end()
                            ->scalarNode('certificate')->end()
                            ->scalarNode('private_key')->end()
                            ->scalarNode('password')->defaultValue(null)->end()
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
        ->end();

        return $treeBuilder;
    }
}
