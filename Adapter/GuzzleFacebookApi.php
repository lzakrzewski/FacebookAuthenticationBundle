<?php

namespace Lucaszz\FacebookAuthenticationBundle\Adapter;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Message\RequestInterface;
use GuzzleHttp\Message\Response;
use GuzzleHttp\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Routing\RequestContext;

class GuzzleFacebookApi implements FacebookApi
{
    const ME_URL = 'https://graph.facebook.com/me';
    const ACCESS_TOKEN_URL = 'https://graph.facebook.com/oauth/access_token';

    const SUCCESSFUL = 200;

    /** @var ClientInterface */
    private $client;
    /** @var RequestContext */
    private $requestContext;
    /** @var array */
    private $config;
    /** @var string */
    private $loginPath;
    /** @var LoggerInterface|null */
    private $logger;

    /**
     * @param ClientInterface      $client
     * @param RequestContext       $requestContext
     * @param string               $loginPath
     * @param array                $config
     * @param LoggerInterface|null $logger
     */
    public function __construct(ClientInterface $client, RequestContext $requestContext, $loginPath, array $config, LoggerInterface $logger = null)
    {
        $this->client = $client;
        $this->requestContext = $requestContext;
        $this->loginPath = $loginPath;
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

    private function redirectUri()
    {
        return sprintf('%s://%s%s', $this->requestContext->getScheme(), $this->requestContext->getHost(), $this->loginPath);
    }

    private function accessTokenRequest($code)
    {
        $request = $this->client->createRequest('GET', self::ACCESS_TOKEN_URL);
        $query = $request->getQuery();

        $query->set('client_id', $this->config['app_id']);
        $query->set('redirect_uri', $this->redirectUri());
        $query->set('client_secret', $this->config['app_secret']);
        $query->set('code', $code);

        return $request;
    }

    private function meRequest($accessToken)
    {
        $request = $this->client->createRequest('GET', self::ME_URL);
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
