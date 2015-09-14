<?php

namespace Lucaszz\FacebookAuthenticationBundle\Adapter;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;
use Symfony\Component\Routing\RequestContext;

class GuzzleFacebookApi implements FacebookApi
{
    const ME_URL = 'https://graph.facebook.com/me';
    const ACCESS_TOKEN_URL = 'https://graph.facebook.com/oauth/access_token';

    /** @var ClientInterface */
    private $client;
    /** @var RequestContext */
    private $requestContext;
    /** @var array */
    private $config;
    /** @var string */
    private $loginPath;

    /**
     * @param ClientInterface $client
     * @param RequestContext  $requestContext
     * @param string          $loginPath
     * @param array           $config
     */
    public function __construct(ClientInterface $client, RequestContext $requestContext, $loginPath, array $config)
    {
        $this->client = $client;
        $this->requestContext = $requestContext;
        $this->loginPath = $loginPath;
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function accessToken($code)
    {
        $request = $this->client->createRequest('GET', self::ACCESS_TOKEN_URL);
        $query = $request->getQuery();

        $query->set('client_id', $this->config['app_id']);
        $query->set('redirect_uri', $this->redirectUri());
        $query->set('client_secret', $this->config['app_secret']);
        $query->set('code', $code);

        try {
            $response = $this->client->send($request);
            $body = (string) $response->getBody();

            $data = array();
            parse_str($body, $data);

            if (isset($data['access_token'])) {
                //$this->logger->info($data['access_token']);

                return $data['access_token'];
            }
        } catch (RequestException $e) {
            print_r($e->getMessage());

            die;
        }

        return;

        throw new \Exception('Not implemented yet.');
    }

    /**
     * {@inheritdoc}
     */
    public function me($accessToken)
    {
        var_dump($accessToken);
        die;

        //throw new \Exception('Not implemented yet.');
    }

    private function redirectUri()
    {
        return sprintf('%s://%s%s', $this->requestContext->getScheme(), $this->requestContext->getHost(), $this->loginPath);
    }
}
