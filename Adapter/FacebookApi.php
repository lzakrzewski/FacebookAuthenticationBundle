<?php

namespace Lucaszz\FacebookAuthenticationBundle\Adapter;

interface FacebookApi
{
    public function accessToken();

    /**
     * @return string
     */
    public function loginDialogUrl();

    /**
     * @param $accessToken
     *
     * @return array
     */
    public function me($accessToken);
}
