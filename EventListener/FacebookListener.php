<?php

namespace Lucaszz\FacebookAuthenticationBundle\EventListener;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestMatcher;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Routing\RouterInterface;

class FacebookListener
{
    const LOGIN_DIALOG_URL = 'https://www.facebook.com/dialog/oauth';

    /** @var RouterInterface */
    private $router;

    /** @var array */
    private $config;

    /**
     * @param RouterInterface $router
     * @param array           $config
     */
    public function __construct(RouterInterface $router, array $config)
    {
        $this->config = $config;
        $this->router = $router;
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $requestMatcher = new RequestMatcher('^'.$this->config['login_path']);
        if (false === $requestMatcher->matches($event->getRequest())) {
            return;
        }

        $event->setResponse(new RedirectResponse($this->loginDialogUrl()));
    }

    private function loginDialogUrl()
    {
        return self::LOGIN_DIALOG_URL.'?'.http_build_query(array(
            'client_id' => $this->config['app_id'],
            'redirect_uri' => $this->router->generate('lucaszz_facebook_authentication_login_path', array(), true),
            'scope' => 'user_birthday, public_profile, email',
        ));
    }
}
