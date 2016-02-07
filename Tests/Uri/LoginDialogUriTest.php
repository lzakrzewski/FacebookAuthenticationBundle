<?php

namespace Lzakrzewski\FacebookAuthenticationBundle\Tests\Uri;

use Lzakrzewski\FacebookAuthenticationBundle\Uri\LoginDialogUri;
use Lzakrzewski\FacebookAuthenticationBundle\Uri\RedirectUri;
use Prophecy\Prophecy\ObjectProphecy;

class LoginDialogUriTest extends \PHPUnit_Framework_TestCase
{
    /** @var ObjectProphecy|RedirectUri */
    private $redirectUri;
    /** @var LoginDialogUri */
    private $uri;

    /** @test */
    public function it_gets_login_dialog_uri()
    {
        $this->assertEquals(
            'https://www.facebook.com/v2.5/dialog/oauth?client_id=123456&scope=public_profile%2C+email%2C+user_birthday',
            $this->uri->get()
        );
    }

    /** {@inheritdoc} */
    protected function setUp()
    {
        $this->redirectUri = $this->prophesize('Lzakrzewski\FacebookAuthenticationBundle\Uri\RedirectUri');

        $this->uri = new LoginDialogUri($this->redirectUri->reveal(), 123456, array('public_profile', 'email', 'user_birthday'));
    }

    /** {@inheritdoc} */
    protected function tearDown()
    {
        $this->redirectUri = null;
        $this->uri = null;
    }
}
