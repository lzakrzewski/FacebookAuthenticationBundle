<?php

namespace Lucaszz\FacebookAuthenticationBundle\Tests\Model;

use Lucaszz\FacebookAuthenticationBundle\Factory\FacebookUrls;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\Routing\RequestContext;

class FacebookUrlsTest extends \PHPUnit_Framework_TestCase
{
    /** @var RequestContext|ObjectProphecy */
    private $requestContext;
    /** @var FacebookUrls */
    private $urls;

    /**
     * @test
     */
    public function it_gets_redirect_uri()
    {
        $this->assertEquals('http://host.com/facebook/login', $this->urls->redirectUri());
    }

    /**
     * @test
     */
    public function it_gets_login_dialog_url()
    {
        $this->assertEquals(
            'https://www.facebook.com/dialog/oauth?client_id=12345&redirect_uri=http%3A%2F%2Fhost.com%2Ffacebook%2Flogin&scope=email%2C+public_profile',
            $this->urls->loginDialogUrl()
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->requestContext = $this->prophesize('\Symfony\Component\Routing\RequestContext');
        $this->requestContext->getScheme()->willReturn('http');
        $this->requestContext->getHost()->willReturn('host.com');

        $config = array('app_id' => '12345', 'scope' => array('email', 'public_profile'));
        $loginPath = '/facebook/login';

        $this->urls = new FacebookUrls($this->requestContext->reveal(), $config, $loginPath);
    }
}
