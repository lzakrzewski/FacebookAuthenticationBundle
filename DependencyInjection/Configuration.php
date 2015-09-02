<?php

namespace Lucaszz\FacebookAuthenticationBundle\DependencyInjection;

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
        $rootNode = $treeBuilder->root('lucaszz_facebook_authentication');

        $rootNode
            ->children()
                ->scalarNode('login_path')->defaultValue('/facebook/login')->end()
                ->scalarNode('app_id')->isRequired()->end()
                ->scalarNode('app_secret')->isRequired()->end()
            ->end();

        return $treeBuilder;
    }
}
