<?php

namespace Lucaszz\FacebookAuthenticationBundle\Adapter;

use GuzzleHttp\ClientInterface;

class GuzzleFacebookApi implements FacebookApi
{
    const ME_URL = 'https://graph.facebook.com/me';
    const ACCESS_TOKEN_URL = 'https://graph.facebook.com/oauth/access_token';

    /** @var ClientInterface */
    private $client;

    /**
     * @param ClientInterface $client
     */
    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * {@inheritdoc}
     */
    public function accessToken($code)
    {
        return 'xyz';
    }

    /**
     * {@inheritdoc}
     */
    public function me($accessToken)
    {
        return array('id' => 123456, 'name' => 'FacebookUser', 'email' => 'email@example.com');
    }
}
