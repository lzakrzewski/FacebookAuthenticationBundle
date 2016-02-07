<?php

namespace Lzakrzewski\FacebookAuthenticationBundle\Tests\Integration\Model;

use FOS\UserBundle\Model\UserInterface;
use Lzakrzewski\FacebookAuthenticationBundle\Model\FacebookUsers;
use Lzakrzewski\FacebookAuthenticationBundle\Tests\Integration\IntegrationTestCase;

class FacebookUsersTest extends IntegrationTestCase
{
    /** @var FacebookUsers */
    private $facebookUsers;

    /** @test */
    public function it_creates_new_user()
    {
        $userNode = array('id' => 10203138199203984, 'name' => 'Facebook user', 'email' => 'facebook@example.com');

        $user = $this->facebookUsers->get($userNode);

        $this->assertFacebookUser(10203138199203984, 'Facebook user', 'facebook@example.com', $user);
    }

    /** @test */
    public function it_gets_existing_user_by_facebook_id()
    {
        $userNode = array('id' => 10203138199203984, 'name' => 'Facebook user', 'email' => 'facebook@example.com');
        $this->user($userNode['name'], uniqid(), $userNode['email'], $userNode['id']);

        $user = $this->facebookUsers->get($userNode);

        $this->assertFacebookUser($userNode['id'], $userNode['name'], $userNode['email'], $user);
    }

    /** @test */
    public function it_gets_existing_user_by_facebook_id_with_already_used_email()
    {
        $userNode = array('id' => 10203138199203984, 'name' => 'Facebook user', 'email' => 'facebook@example.com');
        $this->user($userNode['name'], uniqid(), $userNode['email'], $userNode['id']);

        $user = $this->facebookUsers->get($userNode);

        $this->assertFacebookUser($userNode['id'], $userNode['name'], $userNode['email'], $user);
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->facebookUsers = $this->container->get('lzakrzewski_facebook_authentication.model.facebook_users');

        $this->setupDatabase();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        $this->facebookUsers = null;

        parent::tearDown();
    }

    private function assertFacebookUser($facebookId, $name, $email, UserInterface $user)
    {
        $this->assertInstanceOf('Lzakrzewski\FacebookAuthenticationBundle\Model\FacebookUser', $user);
        $this->assertInstanceOf('FOS\UserBundle\Model\User', $user);

        $this->assertEquals($facebookId, $user->getFacebookId());
        $this->assertEquals($name, $user->getUsername());
        $this->assertEquals($email, $user->getEmail());

        $this->assertTrue($user->isEnabled());
        $this->assertNotNull($user->getPassword());
    }
}
