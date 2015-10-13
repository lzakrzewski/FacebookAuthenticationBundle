<?php

namespace Lucaszz\FacebookAuthenticationBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Config\FileLocator;

class LucaszzFacebookAuthenticationExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        $container->setParameter('lucaszz_facebook_authentication.app_id', $config['app_id']);
        $container->setParameter('lucaszz_facebook_authentication.app_secret', $config['app_secret']);
        $container->setParameter('lucaszz_facebook_authentication.scope', $config['scope']);
        $container->setParameter('lucaszz_facebook_authentication.fields', $config['fields']);
    }
}
