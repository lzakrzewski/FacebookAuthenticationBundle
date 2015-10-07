<?php

namespace Lucaszz\FacebookAuthenticationBundle\Tests\Integration\Authentication;

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Lucaszz\FacebookAuthenticationBundle\Tests\Integration\Adapter\FakeFacebookApi;
use Lucaszz\FacebookAuthenticationBundle\Tests\Integration\IntegrationTestCase;
use Lucaszz\FacebookAuthenticationBundle\Tests\TestUser;

class AuthenticationTest extends IntegrationTestCase
{
    /** @var EntityManager */
    private $entityManager;

    /**
     * @test
     */
    public function it_redirects_to_facebook_dialog_page()
    {
        $this->visit('/facebook/login');

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\RedirectResponse', $this->client->getResponse());

        $query = $this->redirectResponseQuery();

        $this->assertArrayHasKey('redirect_uri', $query);
        $this->assertArrayHasKey('scope', $query);
        $this->assertArrayHasKey('client_id', $query);
    }

    /**
     * @test
     */
    public function it_authorize_new_facebook_user()
    {
        $this->visit('/facebook/login?code=1234');

        $this->assertIsAuthorizedAsUser('FacebookUser');
        $this->assertThatLogWithMessageWasCreated('has been authenticated successfully');
    }

    /**
     * @test
     */
    public function it_authorize_existing_facebook_user()
    {
        $this->user('FacebookUser', 'test1', 123456);

        $this->visit('/facebook/login?code=1234');

        $this->assertIsAuthorizedAsUser('FacebookUser');
        $this->assertThatLogWithMessageWasCreated('has been authenticated successfully');
    }

    /**
     * @test
     */
    public function it_does_not_authorize_facebook_user_when_problem_with_api_occurs()
    {
        $this->user('FacebookUser', 'test1', 123456);

        FakeFacebookApi::problemWithApiOccurs();

        $this->visit('/facebook/login?code=1234');

        $this->assertIsNotAuthorizedAsUser();
        $this->assertThatLogWithMessageWasCreated('Authentication request failed');
    }

    /**
     * @test
     */
    public function it_can_be_authorized_with_form_login_and_valid_credentials()
    {
        $this->user('john-doe', 'test1');

        $this->visitRoute('fos_user_security_login');
        $this->fillAndSubmitLoginForm('john-doe', 'test1');

        $this->assertIsAuthorizedAsUser('john-doe');
    }

    /**
     * @test
     */
    public function it_can_not_be_authorized_with_form_login_and_invalid_credentials()
    {
        $this->user('john-doe', 'test1');

        $this->visitRoute('fos_user_security_login');
        $this->fillAndSubmitLoginForm('wrong-username', 'test1');

        $this->assertIsNotAuthorizedAsUser();
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->entityManager = $this->container->get('doctrine.orm.default_entity_manager');

        $this->setupDatabase();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        $this->entityManager = null;

        parent::tearDown();
    }

    private function fillAndSubmitLoginForm($username, $password)
    {
        $form = $this->crawler->selectButton('_submit')->form();

        $this->client->submit($form, array('_username' => $username, '_password' => $password));
    }

    private function assertIsAuthorizedAsUser($username)
    {
        $securityData = $this->securityData();

        $this->assertEquals($username, $securityData['user']);
    }

    private function assertIsNotAuthorizedAsUser()
    {
        $securityData = $this->securityData();

        $this->assertEquals('Symfony\Component\Security\Core\Authentication\Token\AnonymousToken', $securityData['token_class']);
    }

    private function securityData()
    {
        $this->client->enableProfiler();
        $this->visitRoute('fos_user_security_login');

        return unserialize($this->client->getProfile()->getCollector('security')->serialize());
    }

    private function redirectResponseQuery()
    {
        $parsedUrl = parse_url($this->client->getResponse()->getTargetUrl());
        parse_str($parsedUrl['query'], $parsedQuery);

        return $parsedQuery;
    }

    private function user($username, $password, $facebookId = 12456)
    {
        $user = new TestUser();

        $user->setPlainPassword($password);
        $user->setUsername($username);
        $user->setEmail('john@example.com');
        $user->setEnabled(true);
        $user->setFacebookId($facebookId);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    private function setupDatabase()
    {
        $params = $this->entityManager->getConnection()->getParams();
        $tmpConnection = DriverManager::getConnection($params);
        $tmpConnection->getSchemaManager()->createDatabase($params['path']);
        $schemaTool = new SchemaTool($this->entityManager);
        $schemaTool->dropDatabase();

        $userModelClass = $this->container->getParameter('fos_user.model.user.class');
        $schemaTool->createSchema(array($this->entityManager->getClassMetadata($userModelClass)));

        $this->purgeDatabase();
    }

    private function purgeDatabase()
    {
        $userModelClass = $this->container->getParameter('fos_user.model.user.class');
        $tableName = $this->entityManager->getClassMetadata($userModelClass)->getTableName();

        $connection = $this->entityManager->getConnection();
        $connection->exec(sprintf('DELETE FROM %s', $tableName));
    }
}
