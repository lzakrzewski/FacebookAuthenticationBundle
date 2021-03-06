<?php

namespace Lzakrzewski\FacebookAuthenticationBundle\Model;

interface FacebookUser
{
    /**
     * @return int
     */
    public function getFacebookId();

    /**
     * @param int $facebookId
     */
    public function setFacebookId($facebookId);
}
