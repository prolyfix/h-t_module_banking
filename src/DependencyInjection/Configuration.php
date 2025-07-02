<?php

namespace Prolyfix\BankingBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        var_dump("ici");
        $treeBuilder = new TreeBuilder('acme_social');
        $rootNode = $treeBuilder->getRootNode();


        $rootNode
            ->children()
                ->scalarNode('api_key')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                ->arrayNode('networks')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('twitter')->defaultTrue()->end()
                        ->booleanNode('facebook')->defaultFalse()->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}