<?php

namespace Lucaszz\FacebookAuthenticationBundle\Tests\Factory;

use Lucaszz\FacebookAuthenticationBundle\Factory\FacebookApiFactory;
use Lucaszz\FacebookAuthenticationBundle\Uri\FacebookUri;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Log\LoggerInterface;

class FacebookApiFactoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var ObjectProphecy */
    private $client;
    /** @var ObjectProphecy|FacebookUri */
    private $redirectUri;
    /** @var ObjectProphecy|LoggerInterface */
    private $logger;

    /** @test */
    public function it_gets_facebook_api()
    {
        $this->redirectUri->get()->willReturn('http://host.com/facebook/login');

        $factory = new FacebookApiFactory($this->client->reveal(), $this->redirectUri->reveal(), 123456, 'secret', $this->logger->reveal());

        $this->assertInstanceOf('Lucaszz\FacebookAuthenticationAdapter\Adapter\GuzzleFacebookApi', $factory->get());
    }

    /** @test */
    public function it_gets_facebook_api_without_logger()
    {
        $this->redirectUri->get()->willReturn('http://host.com/facebook/login');

        $factory = new FacebookApiFactory($this->client->reveal(), $this->redirectUri->reveal(), 123456, 'secret', null);

        $this->assertInstanceOf('Lucaszz\FacebookAuthenticationAdapter\Adapter\GuzzleFacebookApi', $factory->get());
    }

    /** {@inheritdoc} */
    protected function setUp()
    {
        $this->client = $this->prophesize('GuzzleHttp\ClientInterface');
        $this->redirectUri = $this->prophesize('Lucaszz\FacebookAuthenticationBundle\Uri\FacebookUri');
        $this->logger = $this->prophesize('Psr\Log\LoggerInterface');
    }
}
