<?php

namespace Lucaszz\FacebookAuthenticationBundle\Tests\Functional;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as BaseWebTestCase;

abstract class WebTestCase extends BaseWebTestCase
{
    /** @var ContainerInterface */
    protected $container;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        var_dump('ssij');
        $client = $this->createClient();
        $this->container = $client->getContainer();
    }
}
