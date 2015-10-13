<?php

namespace Lucaszz\FacebookAuthenticationBundle\Tests\Model;

use FOS\UserBundle\Security\LoginManagerInterface;
use Lucaszz\FacebookAuthenticationAdapter\Adapter\FacebookApi;
use Lucaszz\FacebookAuthenticationBundle\Model\FacebookUsers;
use Lucaszz\FacebookAuthenticationBundle\Security\FacebookLoginManager;
use Lucaszz\FacebookAuthenticationBundle\Tests\fixtures\TestUser;
use Prophecy\Prophecy\ObjectProphecy;

class FacebookLoginManagerTest extends \PHPUnit_Framework_TestCase
{
    /** @var FacebookApi|ObjectProphecy */
    private $api;
    /** @var FacebookUsers|ObjectProphecy */
    private $users;
    /** @var LoginManagerInterface|ObjectProphecy */
    private $loginManager;
    /** @var array */
    private $fields;
    /** @var FacebookLoginManager */
    private $facebookLoginManager;

    /**
     * @test
     */
    public function it_performs_login()
    {
        $user = new TestUser();
        $userNode = array('id' => 1235, 'name' => 'John doe', 'email' => 'john@example.com');

        $this->api->accessToken('correct-code')->willReturn('access-token');
        $this->api->me('access-token', $this->fields)->willReturn($userNode);
        $this->users->get($userNode)->willReturn($user);
        $this->loginManager->logInUser('firewall', $user)->shouldBeCalled();

        $this->facebookLoginManager->login('correct-code');
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->api = $this->prophesize('Lucaszz\FacebookAuthenticationAdapter\Adapter\FacebookApi');
        $this->users = $this->prophesize('Lucaszz\FacebookAuthenticationBundle\Model\FacebookUsers');
        $this->loginManager = $this->prophesize('FOS\UserBundle\Security\LoginManagerInterface');
        $this->fields = array('name', 'email');

        $this->facebookLoginManager = new FacebookLoginManager($this->api->reveal(), $this->users->reveal(), $this->loginManager->reveal(), $this->fields, 'firewall');
    }
}
