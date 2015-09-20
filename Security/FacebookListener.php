<?php

namespace Lucaszz\FacebookAuthenticationBundle\Security;

use Lucaszz\FacebookAuthenticationBundle\Adapter\FacebookApiException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestMatcher;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;

class FacebookListener implements ListenerInterface
{
    const LOGIN_DIALOG_URL = 'https://www.facebook.com/dialog/oauth';

    /** @var FacebookLoginManager */
    private $loginManager;
    /** @var RequestContext */
    private $requestContext;
    /** @var SecurityContextInterface */
    private $securityContext;
    /** @var AuthenticationSuccessHandlerInterface */
    private $successHandler;
    /** @var AuthenticationFailureHandlerInterface */
    private $failureHandler;
    /** @var string */
    private $loginPath;
    /** @var array */
    private $config;
    /** @var LoggerInterface */
    private $logger;

    /**
     * @param FacebookLoginManager                  $loginManager
     * @param RequestContext                        $requestContext
     * @param SecurityContextInterface              $securityContext
     * @param AuthenticationSuccessHandlerInterface $successHandler
     * @param AuthenticationFailureHandlerInterface $failureHandler
     * @param string                                $loginPath
     * @param array                                 $config
     * @param LoggerInterface                       $logger
     */
    public function __construct(
        FacebookLoginManager $loginManager,
        RequestContext $requestContext,
        SecurityContextInterface $securityContext,
        AuthenticationSuccessHandlerInterface $successHandler,
        AuthenticationFailureHandlerInterface $failureHandler,
        $loginPath,
        array $config,
        LoggerInterface $logger = null
    ) {
        $this->loginManager = $loginManager;
        $this->requestContext = $requestContext;
        $this->securityContext = $securityContext;
        $this->successHandler = $successHandler;
        $this->failureHandler = $failureHandler;
        $this->loginPath = $loginPath;
        $this->config = $config;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(GetResponseEvent $event)
    {
        $requestMatcher = new RequestMatcher('^'.$this->loginPath);
        if (false === $requestMatcher->matches($event->getRequest())) {
            return;
        }

        $request = $event->getRequest();

        if (null === $code = $request->query->get('code')) {
            $event->setResponse(new RedirectResponse($this->loginDialogUrl()));

            return;
        }

        try {
            $this->loginManager->login($code);

            $response = $this->onSuccess($request, $this->securityContext->getToken());
        } catch (FacebookApiException $e) {
            $response = $this->onFailure($request, $e);
        }

        $event->setResponse($response);
    }

    private function loginDialogUrl()
    {
        $redirectUri = sprintf('%s://%s%s', $this->requestContext->getScheme(), $this->requestContext->getHost(), $this->loginPath);

        return self::LOGIN_DIALOG_URL.'?'.http_build_query(array(
            'client_id' => $this->config['app_id'],
            'redirect_uri' => $redirectUri,
            'scope' => implode(', ', $this->config['scope']),
        ));
    }

    private function onFailure(Request $request, FacebookApiException $failed)
    {
        if (null !== $this->logger) {
            $this->logger->info(sprintf('Authentication request failed: %s', $failed->getMessage()));
        }

        return $this->failureHandler->onAuthenticationFailure($request, new AuthenticationException($failed->getMessage()));
    }

    private function onSuccess(Request $request, TokenInterface $token)
    {
        if (null !== $this->logger) {
            $this->logger->info(sprintf('User "%s" has been authenticated successfully', $token->getUsername()));
        }

        return $this->successHandler->onAuthenticationSuccess($request, $token);
    }
}
