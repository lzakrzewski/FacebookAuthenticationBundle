<?php

namespace Lucaszz\FacebookAuthenticationBundle\Tests\Model;

use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use Lucaszz\FacebookAuthenticationBundle\Annotation\FacebookIdPropertyName;
use Lucaszz\FacebookAuthenticationBundle\Events;
use Lucaszz\FacebookAuthenticationBundle\Model\FacebookUsers;
use Lucaszz\FacebookAuthenticationBundle\Tests\fixtures\TestUser;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class FacebookUsersTest extends \PHPUnit_Framework_TestCase
{
    /** @var ObjectProphecy|UserManagerInterface */
    private $userManager;
    /** @var ObjectProphecy|EventDispatcherInterface */
    private $dispatcher;
    /** @var ObjectProphecy|FacebookIdPropertyName*/
    private $propertyName;
    /** @var FacebookUsers */
    private $facebookUsers;

    /**
     * @test
     */
    public function it_creates_new_user_from_fields()
    {
        $meFields = array('id' => 10203138199203984, 'name' => 'Facebook user', 'email' => 'facebook@example.com');

        $this->userManager->findUserBy(array('facebookId' => $meFields['id']))->willReturn(null);
        $this->userManager->createUser()->willReturn(new TestUser());
        $this->userManager->updateUser(Argument::type('Lucaszz\FacebookAuthenticationBundle\Tests\fixtures\TestUser'))
            ->shouldBeCalled();

        $this->dispatcher->dispatch(Events::USER_CREATED, Argument::type('Lucaszz\FacebookAuthenticationBundle\Event\FacebookUserEvent'))
            ->shouldBeCalled();

        $user = $this->facebookUsers->get($meFields);

        $this->assertFacebookUser(10203138199203984, 'Facebook user', 'facebook@example.com', $user);
    }

    /**
     * @test
     */
    public function it_gets_existing_user_from_fields_and_refreshes_user_data()
    {
        $existingUser = $this->user(10203138199203984, 'Old facebook username', 'old@email.com');
        $meFields = array('id' => 10203138199203984, 'name' => 'New facebook username', 'email' => 'newfacebook@example.com');

        $this->userManager->findUserBy(array('facebookId' => $meFields['id']))->willReturn($existingUser);
        $this->userManager->createUser()->willReturn(new TestUser());
        $this->userManager->updateUser(Argument::type('Lucaszz\FacebookAuthenticationBundle\Tests\fixtures\TestUser'))
            ->shouldBeCalled();

        $this->dispatcher->dispatch(Events::USER_UPDATED, Argument::type('Lucaszz\FacebookAuthenticationBundle\Event\FacebookUserEvent'))
            ->shouldBeCalled();

        $user = $this->facebookUsers->get($meFields);

        $this->assertFacebookUser(10203138199203984, 'New facebook username', 'newfacebook@example.com', $user);
    }

    /**
     * @test
     * @expectedException \Lucaszz\FacebookAuthenticationBundle\Model\FacebookUserException
     */
    public function it_fails_when_user_is_not_instance_of_facebook_user()
    {
        $meFields = array('id' => 10203138199203984, 'name' => 'Facebook user', 'email' => 'facebook@example.com');
        $wrongUser = $this->prophesize('\FOS\UserBundle\Model\UserInterface');

        $this->userManager->findUserBy(array('facebookId' => $meFields['id']))->willReturn(null);
        $this->userManager->createUser()->willReturn($wrongUser->reveal());

        $this->facebookUsers->get($meFields);
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->userManager = $this->prophesize('\FOS\UserBundle\Model\UserManagerInterface');
        $this->dispatcher = $this->prophesize('\Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $this->propertyName = $this->prophesize('\Lucaszz\FacebookAuthenticationBundle\Annotation\FacebookIdPropertyName');

        $this->propertyName->get(Argument::type('Lucaszz\FacebookAuthenticationBundle\Tests\fixtures\TestUser'))->willReturn('facebookId');

        $this->facebookUsers = new FacebookUsers($this->userManager->reveal(), $this->propertyName->reveal(), $this->dispatcher->reveal());
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        $this->userManager = null;
        $this->propertyName = null;
        $this->dispatcher = null;
        $this->facebookUsers = null;

        parent::tearDown();
    }

    private function user($facebookId, $name, $email)
    {
        $testUser = new TestUser();
        $testUser->setFacebookId($facebookId);
        $testUser->setUsername($name);
        $testUser->setEmail($email);
        $testUser->setEnabled(true);
        $testUser->setPassword(uniqid());

        return $testUser;
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
