<?php

namespace Lucaszz\FacebookAuthenticationBundle\Factory;

use Lucaszz\FacebookAuthenticationBundle\Adapter\FacebookApi;
use Symfony\Component\Routing\RequestContext;

class FacebookUrls
{
    /** @var RequestContext */
    private $requestContext;
    /** @var array */
    private $config;
    /** @var string */
    private $loginPath;

    /**
     * @param RequestContext $requestContext
     * @param array          $config
     * @param string         $loginPath
     */
    public function __construct(RequestContext $requestContext, array $config, $loginPath)
    {
        $this->requestContext = $requestContext;
        $this->config = $config;
        $this->loginPath = $loginPath;
    }

    /**
     * @return string
     */
    public function redirectUri()
    {
        return sprintf('%s://%s%s', $this->requestContext->getScheme(), $this->requestContext->getHost(), $this->loginPath);
    }

    /**
     * @return string
     */
    public function loginDialogUrl()
    {
        return FacebookApi::FACEBOOK_LOGIN_DIALOG_URL.'?'.http_build_query(array(
            'client_id' => $this->config['app_id'],
            'redirect_uri' => $this->redirectUri(),
            'scope' => implode(', ', $this->config['scope']),
        ));
    }
}
