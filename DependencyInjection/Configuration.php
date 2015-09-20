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
                ->scalarNode('app_id')->isRequired()->end()
                ->scalarNode('app_secret')->isRequired()->end()
                ->arrayNode('scope')
                    ->defaultValue(array('email', 'public_profile'))
                    ->beforeNormalization()
                        ->ifTrue(function ($v) {
                            return !in_array('email', $v) || !in_array('public_profile', $v);
                        })
                        ->thenInvalid('Values "email" and "public_profile" are required for "lucaszz_facebook_authentication.scope", %s given.')
                    ->end()
                    ->prototype('scalar')->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
