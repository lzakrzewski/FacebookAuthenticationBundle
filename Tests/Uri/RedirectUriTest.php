<?php

namespace Lucaszz\FacebookAuthenticationBundle\Tests\Uri;

use Lucaszz\FacebookAuthenticationBundle\Uri\RedirectUri;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\Routing\RequestContext;

class RedirectUriTest extends \PHPUnit_Framework_TestCase
{
    /** @var ObjectProphecy|RequestContext */
    private $context;
    /** @var RedirectUri */
    private $uri;

    /** @test */
    public function it_gets_redirect_uri()
    {
        $this->context->getScheme()->willReturn('https');
        $this->context->getHost()->willReturn('host.com');
        $this->context->getBaseUrl()->willReturn('');

        $this->assertEquals('https://host.com/facebook/login', $this->uri->get());
    }

    /** @test */
    public function it_gets_redirect_uri_with_base_url()
    {
        $this->context->getScheme()->willReturn('https');
        $this->context->getHost()->willReturn('host.com');
        $this->context->getBaseUrl()->willReturn('/app_dev.php');

        $this->assertEquals('https://host.com/app_dev.php/facebook/login', $this->uri->get());
    }

    /** {@inheritdoc} */
    protected function setUp()
    {
        $this->context = $this->prophesize('Symfony\Component\Routing\RequestContext');

        $this->uri = new RedirectUri($this->context->reveal(), '/facebook/login');
    }

    /** {@inheritdoc} */
    protected function tearDown()
    {
        $this->context = null;
        $this->uri = null;
    }
}
