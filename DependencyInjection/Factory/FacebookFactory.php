<?php

namespace Lucaszz\FacebookAuthenticationBundle\DependencyInjection\Factory;

use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\SecurityFactoryInterface;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\DefinitionDecorator;

class FacebookFactory implements SecurityFactoryInterface
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function create(ContainerBuilder $container, $id, $config, $userProvider, $defaultEntryPoint)
    {
        $providerId = 'security.authentication.provider.dao.'.$id;
        $listenerId = 'security.authentication.listener.lucaszz_facebook.'.$id;

        $container->setDefinition($listenerId, new DefinitionDecorator('lucaszz_facebook_authentication.security.facebook_listener'));

        return array($providerId, $listenerId, $defaultEntryPoint);
    }

    public function getPosition()
    {
        return 'pre_auth';
    }

    public function getKey()
    {
        return 'lucaszz_facebook';
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function addConfiguration(NodeDefinition $node)
    {
    }
}
