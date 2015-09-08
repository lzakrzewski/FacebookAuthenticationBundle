<?php

namespace Lucaszz\FacebookAuthenticationBundle\Adapter;

interface FacebookApi
{
    /**
     * @param $code
     *
     * @return string
     */
    public function accessToken($code);

    /**
     * @param $accessToken
     *
     * @return array
     */
    public function me($accessToken);
}
