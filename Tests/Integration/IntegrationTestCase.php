<?php

namespace Lucaszz\FacebookAuthenticationBundle\Tests\Integration;

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Lucaszz\FacebookAuthenticationBundle\Tests\fixtures\TestUser;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;
use Symfony\Component\Routing\RouterInterface;

abstract class IntegrationTestCase extends WebTestCase
{
    /** @var ContainerInterface */
    protected $container;
    /** @var Client */
    protected $client;
    /** @var Crawler */
    protected $crawler;
    /** @var RouterInterface */
    private $router;
    /** @var DebugLoggerInterface */
    private $logger;
    /** @var EntityManager */
    private $entityManager;

    /** {@inheritdoc} */
    public static function getKernelClass()
    {
        include_once __DIR__.'/app/TestKernel.php';

        return 'Lucaszz\FacebookAuthenticationBundle\Tests\Integration\app\TestKernel';
    }

    /** {@inheritdoc} */
    protected function setUp()
    {
        $this->client = $this->createClient();
        $this->container = $this->client->getContainer();

        $this->router = $this->container->get('router');
        $this->logger = $this->container->get('logger');
        $this->entityManager = $this->container->get('doctrine.orm.default_entity_manager');
    }

    /** {@inheritdoc} */
    protected function tearDown()
    {
        $this->client = null;
        $this->container = null;
        $this->router = null;
        $this->logger = null;
        $this->entityManager = null;

        parent::tearDown();
    }

    protected function visit($url)
    {
        $this->crawler = $this->client->request('GET', $url);
    }

    protected function visitRoute($routeName, array $parameters = array())
    {
        $this->visit($this->router->generate($routeName, $parameters));
    }

    protected function user($username, $password, $email, $facebookId = 12456)
    {
        $user = new TestUser();

        $user->setPlainPassword($password);
        $user->setUsername($username);
        $user->setEmail($email);
        $user->setEnabled(true);
        $user->setFacebookId($facebookId);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
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

    protected function setupDatabase()
    {
        $params = $this->entityManager->getConnection()->getParams();
        $tmpConnection = DriverManager::getConnection($params);
        $tmpConnection->getSchemaManager()->createDatabase($params['path']);
        $schemaTool = new SchemaTool($this->entityManager);
        $schemaTool->dropDatabase();

        $userModelClass = $this->container->getParameter('fos_user.model.user.class');
        $schemaTool->createSchema(array($this->entityManager->getClassMetadata($userModelClass)));
    }
}
