<?php

namespace Lzakrzewski\FacebookAuthenticationBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Config\FileLocator;

class LzakrzewskiFacebookAuthenticationExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        if (!$container->has('fos_user.doctrine_registry')) {
            $container->setAlias('fos_user.doctrine_registry', new Alias('doctrine'));
        }

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        $container->setParameter('lzakrzewski_facebook_authentication.app_id', $config['app_id']);
        $container->setParameter('lzakrzewski_facebook_authentication.app_secret', $config['app_secret']);
        $container->setParameter('lzakrzewski_facebook_authentication.scope', $config['scope']);
        $container->setParameter('lzakrzewski_facebook_authentication.fields', $config['fields']);
    }
}
