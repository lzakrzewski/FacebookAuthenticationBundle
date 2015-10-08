<?php

namespace Lucaszz\FacebookAuthenticationBundle\Tests\fixtures;

use FOS\UserBundle\Model\User;
use Lucaszz\FacebookAuthenticationBundle\Model\FacebookUser;

class WrongUser extends User implements FacebookUser
{
    /** @var string */
    private $test = 'value';

    /**
     * {@inheritdoc}
     */
    public function getFacebookId()
    {
        return $this->test;
    }

    /**
     * {@inheritdoc}
     */
    public function setFacebookId($facebookId)
    {
        $this->test = $facebookId;
    }
}
