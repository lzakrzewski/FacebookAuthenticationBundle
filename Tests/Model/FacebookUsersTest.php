<?php

namespace Lucaszz\FacebookAuthenticationBundle\Tests\Model;

use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use Lucaszz\FacebookAuthenticationBundle\Model\FacebookUsers;
use Lucaszz\FacebookAuthenticationBundle\Tests\TestUser;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class FacebookUsersTest extends \PHPUnit_Framework_TestCase
{
    /** @var FacebookUsers */
    private $facebookUsers;
    /** @var ObjectProphecy|UserManagerInterface */
    private $userManager;
    /** @var ObjectProphecy|EventDispatcherInterface */
    private $dispatcher;

    /**
     * @test
     */
    public function it_creates_new_user_from_fields()
    {
        $meFields = array('id' => 10203138199203984, 'name' => 'Facebook user', 'email' => 'facebook@example.com');

        $this->userManager->findUserBy(array('facebookId' => $meFields['id']))->willReturn(null);
        $this->userManager->createUser()->willReturn($this->testUser());
        $this->userManager->updateUser(Argument::type('Lucaszz\FacebookAuthenticationBundle\Tests\TestUser'))->shouldBeCalled();

        $user = $this->facebookUsers->get($meFields);

        $this->assertFacebookUser(10203138199203984, 'Facebook user', 'facebook@example.com', $user);
    }

    /**
     * @test
     */
    public function it_gets_existing_user_from_fields_and_refreshes_user_data()
    {
        $existingUser = $this->testUser(10203138199203984, 'Old facebook username', 'old@email.com');

        $meFields = array('id' => 10203138199203984, 'name' => 'New facebook username', 'email' => 'newfacebook@example.com');

        $this->userManager->findUserBy(array('facebookId' => $meFields['id']))->willReturn($existingUser);
        $this->userManager->createUser()->willReturn();
        $this->userManager->updateUser(Argument::type('Lucaszz\FacebookAuthenticationBundle\Tests\TestUser'))->shouldBeCalled();

        $user = $this->facebookUsers->get($this->meFields(10203138199203984, 'New facebook username', 'newfacebook@example.com'));

        $this->assertFacebookUser(10203138199203984, 'New facebook username', 'newfacebook@example.com', $user);
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->userManager = $this->prophesize('\FOS\UserBundle\Model\UserManagerInterface');
        $this->dispatcher = $this->prophesize('\Symfony\Component\EventDispatcher\EventDispatcherInterface');

        $this->facebookUsers = new FacebookUsers($this->userManager->reveal(), $this->dispatcher->reveal());
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        $this->userManager = null;
        $this->dispatcher = null;
        $this->facebookUsers = null;

        parent::tearDown();
    }

    private function meFields($facebookId, $userName, $email)
    {
        $fields = json_decode('{ "id": "123456789", "birthday": "03/18/1976", "email": "test\u0040example.com", "first_name": "DolorAmit", "gender": "male", "last_name": "LoremIpsum", "link": "https://www.facebook.com/app_scoped_user_id/123456789/", "locale": "en_US", "name": "DolorAmit LoremIpsum", "timezone": 2, "updated_time": "2014-11-30T12:42:08+0000", "verified": true }',
            true);

        $fields['id'] = $facebookId;
        $fields['name'] = $userName;
        $fields['email'] = $email;

        return $fields;
    }

    private function testUser()
    {
        return new TestUser();
    }

    private function assertFacebookUser($facebookId, $name, $email, UserInterface $user)
    {
        $this->assertInstanceOf('Lucaszz\FacebookAuthenticationBundle\Model\FacebookUser', $user);
        $this->assertInstanceOf('FOS\UserBundle\Model\User', $user);

        $this->assertEquals($facebookId, $user->getFacebookId());
        $this->assertEquals($name, $user->getUsername());
        $this->assertEquals($email, $user->getEmail());

        $this->assertTrue($user->isEnabled());
        $this->assertNotNull($user->getPassword());
    }
}
