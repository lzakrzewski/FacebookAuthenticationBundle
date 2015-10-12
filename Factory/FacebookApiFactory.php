<?php

namespace Lucaszz\FacebookAuthenticationBundle\Factory;

use GuzzleHttp\ClientInterface;
use Lucaszz\FacebookAuthenticationAdapter\Adapter\FacebookApi;
use Lucaszz\FacebookAuthenticationAdapter\Adapter\GuzzleFacebookApi;
use Lucaszz\FacebookAuthenticationBundle\Uri\FacebookUri;
use Psr\Log\LoggerInterface;

class FacebookApiFactory
{
    /** @var ClientInterface */
    private $client;
    /** @var FacebookUri */
    private $redirectUri;
    /** @var int */
    private $appId;
    /** @var string */
    private $appSecret;
    /** @var LoggerInterface|null */
    private $logger;

    /**
     * @param ClientInterface      $client
     * @param FacebookUri          $redirectUri
     * @param int                  $appId
     * @param string               $appSecret
     * @param null|LoggerInterface $logger
     */
    public function __construct(ClientInterface $client, FacebookUri $redirectUri, $appId, $appSecret, LoggerInterface $logger = null)
    {
        $this->client = $client;
        $this->redirectUri = $redirectUri;
        $this->appId = $appId;
        $this->appSecret = $appSecret;
        $this->logger = $logger;
    }

    /**
     * @return FacebookApi
     */
    public function get()
    {
        return new GuzzleFacebookApi($this->client, $this->redirectUri->get(), $this->appId, $this->appSecret, $this->logger);
    }
}
