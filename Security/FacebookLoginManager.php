<?php

namespace Lucaszz\FacebookAuthenticationBundle\Security;

use FOS\UserBundle\Security\LoginManagerInterface;
use Lucaszz\FacebookAuthenticationAdapter\Adapter\FacebookApi;
use Lucaszz\FacebookAuthenticationBundle\Model\FacebookUsers;

class FacebookLoginManager
{
    /** @var FacebookApi */
    private $api;
    /** @var FacebookUsers */
    private $users;
    /** @var LoginManagerInterface */
    private $loginManager;
    /** @var array */
    private $fields;
    /** @var string */
    private $firewallName;

    /**
     * @param FacebookApi           $api
     * @param FacebookUsers         $users
     * @param LoginManagerInterface $loginManager
     * @param array                 $fields
     * @param string                $firewallName
     */
    public function __construct(FacebookApi $api, FacebookUsers $users, LoginManagerInterface $loginManager, array $fields, $firewallName)
    {
        $this->api = $api;
        $this->users = $users;
        $this->loginManager = $loginManager;
        $this->fields = $fields;
        $this->firewallName = $firewallName;
    }

    /**
     * @param string $code
     */
    public function login($code)
    {
        $accessToken = $this->api->accessToken($code);
        $user = $this->users->get($this->api->me($accessToken, $this->fields));

        $this->loginManager->logInUser($this->firewallName, $user);
    }
}
