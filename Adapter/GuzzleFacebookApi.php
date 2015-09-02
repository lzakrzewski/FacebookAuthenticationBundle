<?php

namespace Lucaszz\FacebookAuthenticationBundle\Adapter;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;

class GuzzleFacebookApi implements FacebookApi
{
    const ME_URL = 'https://graph.facebook.com/me';
    const ACCESS_TOKEN_URL = 'https://graph.facebook.com/oauth/access_token';

    /** @var ClientInterface */
    private $client;
    /** @var AuthUrl */
    private $urls;

    /**
     * @param ClientInterface $client
     * @param AuthUrl         $urls
     */
    public function __construct(ClientInterface $client, AuthUrl $urls)
    {
        $this->client = $client;
        $this->urls = $urls;
    }

    public function accessToken()
    {
        try {
            $data = $this->handleJsonRequest(
                'GET',
                self::ACCESS_TOKEN_URL,
                array()
            );
        } catch (RequestException $e) {
            throw new FacebookApiException(sprintf('Unable to get "me" endpoint data with access token: %s', 'xyz'));
        }

        return $data;
    }

    /**
     * @return string
     */
    public function loginDialogUrl()
    {
        return $this->urls->loginDialogUrl();
    }

    /**
     * {@inheritdoc}
     */
    public function me($accessToken)
    {
        try {
            $data = $this->handleJsonRequest($accessToken, 'GET', self::ME_URL, array('access_token' => $accessToken));
        } catch (RequestException $e) {
            throw new FacebookApiException(sprintf('Unable to get "me" endpoint data with access token: %s', $accessToken));
        }

        return $data;
    }

    private function handleJsonRequest($accessToken, $method, $url)
    {
        $request = $this->client->createRequest($method, $url);
        $query = $request->getQuery();
        $query->set('access_token', $accessToken);

        $response = $this->client->send($request);

        return $response->json();
    }
}
