<?php

namespace Lucaszz\FacebookAuthenticationBundle\Adapter;

use Symfony\Component\Routing\RequestContext;

class AuthUrl
{
    /** @var RequestContext */
    private $context;

    public function __construct(RequestContext $context, array $config)
    {
        $this->context = $context;
        $this->config = $config;
    }

    /**
     * @return string
     */
    public function loginDialogUrl()
    {
        return $this->baseUrl().$this->config['login_path'];
    }

    private function baseUrl()
    {
        return $this->context->getScheme().'://'.$this->context->getHost();
    }
}
