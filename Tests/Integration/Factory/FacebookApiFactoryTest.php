<?php

namespace Lzakrzewski\FacebookAuthenticationBundle\Tests\Integration\Factory;

use Lzakrzewski\FacebookAuthenticationBundle\Factory\FacebookApiFactory;
use Lzakrzewski\FacebookAuthenticationBundle\Tests\Integration\IntegrationTestCase;

class FacebookApiFactoryTest extends IntegrationTestCase
{
    /** @var FacebookApiFactory */
    private $factory;

    /** @test */
    public function it_gets_facebook_api()
    {
        $api = $this->factory->get();

        $this->assertInstanceOf('Lzakrzewski\FacebookAuthenticationAdapter\Adapter\GuzzleFacebookApi', $api);
    }

    /** {@inheritdoc} */
    protected function setUp()
    {
        parent::setUp();

        $this->factory = $this->container->get('lzakrzewski_facebook_authentication.factory.facebook_api');
    }

    /** {@inheritdoc} */
    public function tearDown()
    {
        $this->factory = null;
    }
}
