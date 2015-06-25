<?php

namespace Lucaszz\FacebookAuthenticationBundle\Model;

interface FacebookUser
{
    /**
     * @return string
     */
    public function getFacebookId();

    /**
     * @param string $facebookId
     */
    public function setFacebookId($facebookId);
}
