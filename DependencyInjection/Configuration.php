<?php

namespace ERD\RateableEntitiesBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;


class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('erd_rateable_entities');

        $rootNode
            ->children()
                ->booleanNode('use_doctrine_events')->defaultValue(false)->end()
                /** @todo implement ->arrayNode('properties_triggering_update') */
            ->end()
        ;

        return $treeBuilder;
    }
}