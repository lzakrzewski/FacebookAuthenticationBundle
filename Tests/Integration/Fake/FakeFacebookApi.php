<?php

namespace Lucaszz\FacebookAuthenticationBundle\Tests\Integration\Fake;

use Lucaszz\FacebookAuthenticationBundle\Adapter\FacebookApi;
use Lucaszz\FacebookAuthenticationBundle\Adapter\FacebookApiException;

class FakeFacebookApi implements FacebookApi
{
    /** @var bool */
    private static $failed = false;

    /**
     * {@inheritdoc}
     */
    public function accessToken($code)
    {
        (self::$failed) ? $this->failed() : null;

        return 'xyz';
    }

    /**
     * {@inheritdoc}
     */
    public function me($accessToken)
    {
        (self::$failed) ? $this->failed() : null;

        return array('id' => 123456, 'name' => 'FacebookUser', 'email' => 'email@example.com');
    }

    public static function problemWithApiOccurs()
    {
        self::$failed = true;
    }

    private function failed()
    {
        throw new FacebookApiException();
    }
}
