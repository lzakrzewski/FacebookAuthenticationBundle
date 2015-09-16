<?php

namespace Lucaszz\FacebookAuthenticationBundle\DependencyInjection\Factory;

use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\SecurityFactoryInterface;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Reference;

class FacebookFactory implements SecurityFactoryInterface
{
    protected $options = array(
        'check_path' => '/login_check',
        'use_forward' => false,
        'require_previous_session' => true,
    );

    protected $defaultSuccessHandlerOptions = array(
        'always_use_default_target_path' => false,
        'default_target_path' => '/',
        'login_path' => '/login',
        'target_path_parameter' => '_target_path',
        'use_referer' => false,
    );

    protected $defaultFailureHandlerOptions = array(
        'failure_path' => null,
        'failure_forward' => false,
        'login_path' => '/login',
        'failure_path_parameter' => '_failure_path',
    );

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function create(ContainerBuilder $container, $id, $config, $userProvider, $defaultEntryPoint)
    {
        $providerId = 'security.authentication.provider.dao.'.$id;
        $listenerId = 'security.authentication.listener.lucaszz_facebook.'.$id;

        $listener = new DefinitionDecorator('lucaszz_facebook_authentication.security.facebook_listener');

        $listener->replaceArgument(3, new Reference($this->createAuthenticationSuccessHandler($container, $id, $config)));
        $listener->replaceArgument(4, new Reference($this->createAuthenticationFailureHandler($container, $id, $config)));
        $listener->replaceArgument(5, $config['facebook_login_path']);

        $container->setParameter('lucaszz_facebook.facebook_login_path', $config['facebook_login_path']);
        $container->setDefinition($listenerId, $listener);

        return array($providerId, $listenerId, $defaultEntryPoint);
    }

    protected function createAuthenticationSuccessHandler($container, $id, $config)
    {
        if (isset($config['success_handler'])) {
            return $config['success_handler'];
        }

        $successHandlerId = 'security.authentication.success_handler.'.$id.'.'.str_replace('-', '_', $this->getKey());

        $successHandler = $container->setDefinition($successHandlerId, new DefinitionDecorator('security.authentication.success_handler'));
        $successHandler->replaceArgument(1, array_intersect_key($config, $this->defaultSuccessHandlerOptions));
        $successHandler->addMethodCall('setProviderKey', array($id));

        return $successHandlerId;
    }

    protected function createAuthenticationFailureHandler($container, $id, $config)
    {
        if (isset($config['failure_handler'])) {
            return $config['failure_handler'];
        }

        $id = 'security.authentication.failure_handler.'.$id.'.'.str_replace('-', '_', $this->getKey());

        $failureHandler = $container->setDefinition($id, new DefinitionDecorator('security.authentication.failure_handler'));
        $failureHandler->replaceArgument(2, array_intersect_key($config, $this->defaultFailureHandlerOptions));

        return $id;
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
        $node
            ->children()
                ->scalarNode('facebook_login_path')
                    ->defaultValue('/facebook/login')
                ->end()
            ->end();
    }
}
