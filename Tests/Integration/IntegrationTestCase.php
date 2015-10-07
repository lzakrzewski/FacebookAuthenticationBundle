<?php

namespace Lucaszz\FacebookAuthenticationBundle\Tests\Integration;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;
use Symfony\Component\Routing\RouterInterface;

abstract class IntegrationTestCase extends WebTestCase
{
    /** @var ContainerInterface */
    protected $container;
    /** @var Client */
    protected $client;
    /** @var array */
    protected $config;
    /** @var RouterInterface */
    private $router;
    /** @var Crawler */
    protected $crawler;
    /** @var DebugLoggerInterface */
    protected $logger;

    /**
     * {@inheritdoc}
     */
    public static function getKernelClass()
    {
        include_once __DIR__.'/app/TestKernel.php';

        return 'Lucaszz\FacebookAuthenticationBundle\Tests\Integration\app\TestKernel';
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->client = $this->createClient();
        $this->container = $this->client->getContainer();
        $this->config = $this->container->getParameter('lucaszz_facebook_authentication.config');
        $this->router = $this->container->get('router');
        $this->logger = $this->container->get('logger');
    }

    protected function visit($url)
    {
        $this->crawler = $this->client->request('GET', $url);
    }

    protected function visitRoute($routeName, array $parameters = array())
    {
        $this->visit($this->router->generate($routeName, $parameters));
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        $this->client = null;
        $this->config = null;
        $this->container = null;
        $this->router = null;
        $this->logger = null;

        parent::tearDown();
    }

    protected function assertThatLogWithMessageWasCreated($expectedMessage)
    {
        $logWasCreated = false;
        foreach ($this->logger->getLogs() as $log) {
            if (false !== strpos($log['message'], $expectedMessage)) {
                $logWasCreated = true;
                break;
            }
        }

        $this->assertTrue($logWasCreated);
    }
}
