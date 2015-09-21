<?php

namespace Lucaszz\FacebookAuthenticationBundle\Tests\Integration;

use Doctrine\ORM\EntityManager;
use Lucaszz\FacebookAuthenticationBundle\Tests\TestUser;

abstract class DatabaseTestCase extends IntegrationTestCase
{
    /** @var EntityManager */
    private $entityManager;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->entityManager = $this->container->get('doctrine.orm.default_entity_manager');

        $this->purgeDatabase();
    }

    protected function entityManager()
    {
        return $this->entityManager;
    }

    protected function user($username, $password, $facebookId = 12456)
    {
        $user = new TestUser();

        $user->setPlainPassword($password);
        $user->setUsername($username);
        $user->setEmail('john@example.com');
        $user->setEnabled(true);
        $user->setFacebookId($facebookId);

        $this->entityManager()->persist($user);
        $this->entityManager()->flush();

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
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
