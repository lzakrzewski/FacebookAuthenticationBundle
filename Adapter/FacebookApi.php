<?php

namespace Lucaszz\FacebookAuthenticationBundle\Adapter;

interface FacebookApi
{
    const GRAPH_API_ME_URL = 'https://graph.facebook.com/me';
    const GRAPH_API_ACCESS_TOKEN_URL = 'https://graph.facebook.com/oauth/access_token';

    /**
     * @param $code
     *
     * @throws FacebookApiException
     *
     * @return string
     */
    public function accessToken($code);

    /**
     * @param $accessToken
     *
     * @throws FacebookApiException
     *
     * @return array
     */
    public function me($accessToken);
}
