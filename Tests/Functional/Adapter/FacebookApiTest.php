<?php

namespace Lucaszz\FacebookAuthenticationBundle\Tests\Functional\Adapter;

use Lucaszz\FacebookAuthenticationBundle\Adapter\FacebookApi;
use Lucaszz\FacebookAuthenticationBundle\Tests\Functional\WebTestCase;

/**
 * @group functional
 */
class FacebookApiTest extends WebTestCase
{
    /** @var FacebookApi */
    private $adapter;

    /**
     * @test
     */
    public function it_can_get_access_token()
    {
        $this->adapter->accessToken();
    }

    /**
     * @test
     */
    public function it_can_get_login_dialog_url()
    {
    }

    /**
     * @test
     */
    public function it_can_get_me_data()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->adapter = $this->container->get('lucaszz_facebook_authentication.adapter.facebook_api');
    }
}
