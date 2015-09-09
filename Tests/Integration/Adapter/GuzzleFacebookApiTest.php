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
        $this->assertEquals('xyz', $this->adapter->accessToken('1234'));
    }

    /**
     * @test
     */
    public function it_can_get_me_data()
    {
        $data = $this->adapter->me($this->accessToken());

        $this->assertEquals(array('id' => 123456, 'name' => 'FacebookUser'), $data);
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
        return 'xyzy';
    }
}
