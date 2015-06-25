<?php

namespace Lucaszz\FacebookAuthenticationBundle\Adapter;

use GuzzleHttp\ClientInterface;

class FacebookApi
{
    /** @var ClientInterface */
    private $client;

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    public function accessToken()
    {
        return 'xyz';
    }

    public function loginDialogUrl()
    {
        return 'xyz';
    }

    public function me()
    {
        return array();
    }
}
