<?php

namespace Lucaszz\FacebookAuthenticationBundle\Tests\Integration;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as BaseWebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Routing\RouterInterface;

abstract class IntegrationTestCase extends BaseWebTestCase
{
    /** @var ContainerInterface */
    protected $container;
    /** @var Client */
    protected $client;
    /** @var RouterInterface */
    private $router;
    /** @var EntityManager */
    private $entityManager;
    /** @var Crawler */
    protected $crawler;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->client = $this->createClient();
        $this->container = $this->client->getContainer();
        $this->router = $this->container->get('router');
        $this->entityManager = $this->container->get('doctrine.orm.default_entity_manager');

        $this->purgeDatabase();
    }

    protected function entityManager()
    {
        return $this->entityManager;
    }

    protected function visit($routeName, array $parameters = array())
    {
        $this->crawler = $this->client->request('GET', $this->router->generate($routeName, $parameters));
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        $this->client = null;
        $this->container = null;
        $this->router = null;
        $this->entityManager = null;

        parent::tearDown();
    }

    private function purgeDatabase()
    {
        $connection = $this->entityManager->getConnection();
        $tables = $connection->getSchemaManager()->listTableNames();

        foreach ($tables as $table) {
            $connection->exec(sprintf('DELETE FROM %s', $table));
        }
    }
}
