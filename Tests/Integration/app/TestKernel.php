<?php

namespace Lucaszz\FacebookAuthenticationBundle\Tests\Integration\app;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use FOS\UserBundle\FOSUserBundle;
use Lucaszz\FacebookAuthenticationBundle\LucaszzFacebookAuthenticationBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\MonologBundle\MonologBundle;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

class TestKernel extends Kernel
{
    /**
     * {@inheritdoc}
     */
    public function registerBundles()
    {
        return array(
            new FrameworkBundle(),
            new DoctrineBundle(),
            new SecurityBundle(),
            new MonologBundle(),
            new TwigBundle(),
            new FOSUserBundle(),
            new LucaszzFacebookAuthenticationBundle(),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheDir()
    {
        return $this->tmpDir().'/cache';
    }

    /**
     * {@inheritdoc}
     */
    public function getLogDir()
    {
        return $this->tmpDir().'/logs';
    }

    /**
     * {@inheritdoc}
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config.yml');
    }

    private function tmpDir()
    {
        return sys_get_temp_dir().'/lucaszz_facebook_authentication_test';
    }
}
