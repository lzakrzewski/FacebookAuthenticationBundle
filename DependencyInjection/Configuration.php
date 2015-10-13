<?php

namespace Lucaszz\FacebookAuthenticationBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
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
                ->arrayNode('fields')
                    ->defaultValue(array('name', 'email'))
                    ->beforeNormalization()
                        ->ifTrue(function ($v) {
                            return !in_array('name', $v) || !in_array('email', $v);
                        })
                        ->thenInvalid('Values "name" and "email" are required for "lucaszz_facebook_authentication.fields", %s given.')
                    ->end()
                    ->prototype('scalar')->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
