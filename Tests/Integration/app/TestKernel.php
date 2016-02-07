<?php

namespace Lzakrzewski\FacebookAuthenticationBundle\Tests\Integration\app;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use FOS\UserBundle\FOSUserBundle;
use Lzakrzewski\FacebookAuthenticationBundle\LzakrzewskiFacebookAuthenticationBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

class TestKernel extends Kernel
{
    /** {@inheritdoc} */
    public function registerBundles()
    {
        return array(
            new FrameworkBundle(),
            new DoctrineBundle(),
            new SecurityBundle(),
            new TwigBundle(),
            new FOSUserBundle(),
            new LzakrzewskiFacebookAuthenticationBundle(),
        );
    }

    /** {@inheritdoc} */
    public function getCacheDir()
    {
        return $this->tmpDir().'/cache';
    }

    /** {@inheritdoc} */
    public function getLogDir()
    {
        return $this->tmpDir().'/logs';
    }

    /** {@inheritdoc} */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config.yml');
    }

    private function tmpDir()
    {
        return sys_get_temp_dir().'/lzakrzewski_facebook_authentication_test';
    }
}
