<?php

namespace Lucaszz\FacebookAuthenticationBundle\Tests\Integration\Adapter;

use Lucaszz\FacebookAuthenticationBundle\Adapter\GuzzleFacebookApi;
use Lucaszz\FacebookAuthenticationBundle\Tests\Integration\IntegrationTestCase;

class GuzzleFacebookApiTest extends IntegrationTestCase
{
    /** @var GuzzleFacebookApi */
    private $adapter;

    /**
     * @test
     */
    public function it_can_get_access_token()
    {
        $this->assertEquals('xyz', $this->adapter->accessToken());
    }

    /**
     * @test
     */
    public function it_can_get_login_dialog_url()
    {
        $this->assertEquals('http://localhost/faebook/login', $this->adapter->loginDialogUrl());
    }

    /**
     * @test
     */
    public function it_can_get_me_data()
    {
        $data = $this->adapter->me($this->accessToken());

        $this->assertEquals(array('id' => 'xyz'), $data);
    }

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->adapter = $this->container->get('lucaszz_facebook_authentication.adapter.facebook_api');
    }

    private function accessToken()
    {
        return $this->container->getParameter('fb_test_access_token');
    }
}
