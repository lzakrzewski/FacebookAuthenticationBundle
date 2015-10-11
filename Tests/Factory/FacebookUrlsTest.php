<?php

namespace Lucaszz\FacebookAuthenticationBundle\Tests\Factory;

use Lucaszz\FacebookAuthenticationBundle\Factory\FacebookUrls;

class FacebookUrlsTest extends \PHPUnit_Framework_TestCase
{
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
        $this->urls = new FacebookUrls('12345', 'http://host.com/facebook/login', array('email', 'public_profile'));
    }
}
