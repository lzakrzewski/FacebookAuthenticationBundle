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
    /** @var array */
    protected $config;
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
        $this->config = $this->container->getParameter('lucaszz_facebook_authentication.config');
        $this->router = $this->container->get('router');
        $this->entityManager = $this->container->get('doctrine.orm.default_entity_manager');

        $this->purgeDatabase();
    }

    protected function entityManager()
    {
        return $this->entityManager;
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
        $this->entityManager = null;

        parent::tearDown();
    }

    private function purgeDatabase()
    {
        $userModelClass = $this->container->getParameter('fos_user.model.user.class');
        $tableName = $this->entityManager->getClassMetadata($userModelClass)->getTableName();

        $connection = $this->entityManager->getConnection();
        $connection->exec(sprintf('DELETE FROM %s', $tableName));
    }
}
