<?php

namespace Lucaszz\FacebookAuthenticationBundle\Tests\Model;

use FOS\UserBundle\Security\LoginManagerInterface;
use Lucaszz\FacebookAuthenticationBundle\Adapter\FacebookApi;
use Lucaszz\FacebookAuthenticationBundle\Model\FacebookUsers;
use Lucaszz\FacebookAuthenticationBundle\Security\FacebookLoginManager;
use Lucaszz\FacebookAuthenticationBundle\Tests\TestUser;
use Prophecy\Prophecy\ObjectProphecy;

class FacebookLoginManagerTest extends \PHPUnit_Framework_TestCase
{
    /** @var FacebookApi|ObjectProphecy */
    private $api;
    /** @var FacebookUsers|ObjectProphecy */
    private $users;
    /** @var LoginManagerInterface|ObjectProphecy */
    private $loginManager;
    /** @var FacebookLoginManager */
    private $facebookLoginManager;

    /**
     * @test
     */
    public function it_performs_login()
    {
        $user = new TestUser();
        $fields = array('id' => 1235, 'name' => 'John doe', 'email' => 'john@example.com');

        $this->api->accessToken('correct-code')->willReturn('access-token');
        $this->api->me('access-token')->willReturn($fields);
        $this->users->get($fields)->willReturn($user);
        $this->loginManager->loginUser('firewall', $user)->shouldBeCalled();

        $this->facebookLoginManager->login('correct-code');
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->api = $this->prophesize('\Lucaszz\FacebookAuthenticationBundle\Adapter\FacebookApi');
        $this->users = $this->prophesize('\Lucaszz\FacebookAuthenticationBundle\Model\FacebookUsers');
        $this->loginManager = $this->prophesize('\FOS\UserBundle\Security\LoginManagerInterface');

        $this->facebookLoginManager = new FacebookLoginManager($this->api->reveal(), $this->users->reveal(), $this->loginManager->reveal(), 'firewall');
    }
}
