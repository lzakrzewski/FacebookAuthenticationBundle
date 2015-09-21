<?php

namespace Lucaszz\FacebookAuthenticationBundle\Adapter;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Message\RequestInterface;
use GuzzleHttp\Message\ResponseInterface;
use Lucaszz\FacebookAuthenticationBundle\Factory\FacebookUrls;
use Psr\Log\LoggerInterface;

class GuzzleFacebookApi implements FacebookApi
{
    /** @var ClientInterface */
    private $client;
    /** @var FacebookUrls */
    private $urls;
    /** @var array */
    private $config;
    /** @var LoggerInterface|null */
    private $logger;

    /**
     * @param ClientInterface      $client
     * @param FacebookUrls         $urls
     * @param array                $config
     * @param LoggerInterface|null $logger
     */
    public function __construct(ClientInterface $client, FacebookUrls $urls, array $config, LoggerInterface $logger = null)
    {
        $this->client = $client;
        $this->urls = $urls;
        $this->config = $config;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function accessToken($code)
    {
        $request = $this->accessTokenRequest($code);

        try {
            $response = $this->client->send($request);
            $accessToken = $this->accessTokenFromResponse($response);

            if (null === $accessToken) {
                throw $this->facebookApiException('Unable to get access token from response.', $request, $response);
            }

            return $accessToken;
        } catch (RequestException $e) {
            throw $this->facebookApiException('An error with facebook graph api occurred: ', $request, null, $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function me($accessToken)
    {
        $request = $this->meRequest($accessToken);

        try {
            $response = $this->client->send($request);
        } catch (RequestException $e) {
            throw $this->facebookApiException('An error with facebook graph api occurred: ', $request, null, $e);
        }

        try {
            $fields = $response->json();
        } catch (\RuntimeException $e) {
            throw $this->facebookApiException(sprintf('Facebook graph api response body is not in JSON format: %s given.', $response->getBody()), $request, $response);
        }

        if (!isset($fields['id']) || !isset($fields['name']) || !isset($fields['email'])) {
            throw $this->facebookApiException(sprintf('Facebook graph api should return response with all required fields. Id, name, email are required %s given', implode(', ', array_keys($fields))), $request, $response);
        }

        return $fields;
    }

    private function accessTokenRequest($code)
    {
        $request = $this->client->createRequest('GET', FacebookApi::GRAPH_API_ACCESS_TOKEN_URL);
        $query = $request->getQuery();

        $query->set('client_id', $this->config['app_id']);
        $query->set('redirect_uri', $this->urls->redirectUri());
        $query->set('client_secret', $this->config['app_secret']);
        $query->set('code', $code);

        return $request;
    }

    private function meRequest($accessToken)
    {
        $request = $this->client->createRequest('GET', FacebookApi::GRAPH_API_ME_URL);
        $query = $request->getQuery();

        $query->set('access_token', $accessToken);

        return $request;
    }

    private function accessTokenFromResponse(ResponseInterface $response)
    {
        $body = (string) $response->getBody();

        $data = array();
        parse_str($body, $data);

        if (isset($data['access_token'])) {
            return $data['access_token'];
        }
    }

    private function facebookApiException($message, RequestInterface $request, ResponseInterface $response = null, RequestException $exception = null)
    {
        if (null !== $exception) {
            $message .= $exception->getMessage();
        }

        if (null !== $this->logger) {
            $context = array(
                'request' => $request,
            );

            if (null !== $response) {
                $context['response'] = (string) $response;
            }

            $this->logger->error($message, $context);
        }

        return new FacebookApiException($message);
    }
}
