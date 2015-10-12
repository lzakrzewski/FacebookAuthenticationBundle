<?php

namespace Lucaszz\FacebookAuthenticationBundle\Uri;

use Symfony\Component\Routing\RequestContext;

class RedirectUri implements FacebookUri
{
    /** @var RequestContext */
    private $context;
    /** @var string */
    private $loginPath;

    /**
     * @param RequestContext $context
     * @param string         $loginPath
     */
    public function __construct(RequestContext $context, $loginPath)
    {
        $this->context = $context;
        $this->loginPath = $loginPath;
    }

    /** {@inheritdoc} */
    public function get()
    {
        return sprintf('%s://%s%s%s', $this->context->getScheme(), $this->context->getHost(), $this->context->getBaseUrl(), $this->loginPath);
    }
}
