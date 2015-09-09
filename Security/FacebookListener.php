<?php

namespace Lucaszz\FacebookAuthenticationBundle\Security;

use Lucaszz\FacebookAuthenticationBundle\Adapter\FacebookApiException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestMatcher;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;

//@todo redirects from security
class FacebookListener implements ListenerInterface
{
    const LOGIN_DIALOG_URL = 'https://www.facebook.com/dialog/oauth';

    /** @var FacebookLoginManager */
    private $loginManager;
    /** @var RequestContext */
    private $context;
    /** @var array */
    private $config;

    /**
     * @param RequestContext       $context
     * @param FacebookLoginManager $loginManager
     * @param array                $config
     */
    public function __construct(RequestContext $context, FacebookLoginManager $loginManager, array $config)
    {
        $this->context = $context;
        $this->loginManager = $loginManager;
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(GetResponseEvent $event)
    {
        $requestMatcher = new RequestMatcher('^'.$this->config['login_path']);
        if (false === $requestMatcher->matches($event->getRequest())) {
            return;
        }

        $request = $event->getRequest();

        if (null !== $code = $request->query->get('code')) {
            try {
                $this->loginManager->login($code);
                $event->setResponse(new RedirectResponse('/login'));

                return;
            } catch (FacebookApiException $e) {
            }
        }

        $event->setResponse(new RedirectResponse($this->loginDialogUrl()));
    }

    private function loginDialogUrl()
    {
        $redirectUri = sprintf('%s://%s%s', $this->context->getScheme(), $this->context->getHost(), $this->config['login_path']);

        return self::LOGIN_DIALOG_URL.'?'.http_build_query(array(
            'client_id' => $this->config['app_id'],
            'redirect_uri' => $redirectUri,
            'scope' => 'user_birthday, public_profile, email',
        ));
    }
}
