<?php

namespace Lucaszz\FacebookAuthenticationBundle\Tests\Model;

use Lucaszz\FacebookAuthenticationBundle\Model\FacebookUsers;
use Lucaszz\FacebookAuthenticationBundle\Tests\Integration\DatabaseTestCase;

class FacebookUsersTest extends DatabaseTestCase
{
    /** @var FacebookUsers */
    private $users;

    /**
     * @test
     */
    public function it_creates_new_user_from_fields()
    {
        $user = $this->users->get($this->meFields(10203138199203984, 'Facebook user', 'facebook@example.com'));

        $this->assertInstanceOf('Lucaszz\FacebookAuthenticationBundle\Model\FacebookUser', $user);
        $this->assertInstanceOf('FOS\UserBundle\Model\User', $user);

        $this->assertEquals(10203138199203984, $user->getFacebookId());
        $this->assertEquals('Facebook user', $user->getUsername());
        $this->assertEquals('facebook@example.com', $user->getEmail());

        $this->assertNotNull($user->getPassword());
        $this->assertNotNull($user->getId());
    }

    /**
     * @test
     */
    public function it_gets_existing_user_from_fields_and_refreshes_user_data()
    {
        $oldUser = $this->user('Old facebook username', 'xyz', 10203138199203984);

        $user = $this->users->get($this->meFields(10203138199203984, 'New facebook username', 'newfacebook@example.com'));

        $this->assertInstanceOf('Lucaszz\FacebookAuthenticationBundle\Model\FacebookUser', $user);
        $this->assertInstanceOf('FOS\UserBundle\Model\User', $user);

        $this->assertEquals(10203138199203984, $user->getFacebookId());
        $this->assertEquals('New facebook username', $user->getUsername());
        $this->assertEquals('newfacebook@example.com', $user->getEmail());

        $this->assertEquals($oldUser->getPassword(), $user->getPassword());
        $this->assertEquals($oldUser->getId(), $user->getId());
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->users = $this->container->get('lucaszz_facebook_authentication.model.facebook_users');
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        $this->users = null;

        parent::tearDown();
    }

    private function meFields($facebookId, $userName, $email)
    {
        $fields = json_decode('{ "id": "123456789", "birthday": "03/18/1976", "email": "test\u0040example.com", "first_name": "DolorAmit", "gender": "male", "last_name": "LoremIpsum", "link": "https://www.facebook.com/app_scoped_user_id/123456789/", "locale": "en_US", "name": "DolorAmit LoremIpsum", "timezone": 2, "updated_time": "2014-11-30T12:42:08+0000", "verified": true }', true);

        $fields['id'] = $facebookId;
        $fields['name'] = $userName;
        $fields['email'] = $email;

        return $fields;
    }
}
