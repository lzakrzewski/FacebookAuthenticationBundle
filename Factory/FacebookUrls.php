<?php

namespace Lucaszz\FacebookAuthenticationBundle\Factory;

class FacebookUrls
{
    const FACEBOOK_LOGIN_DIALOG_URL = 'https://www.facebook.com/dialog/oauth';
    /** @var string */
    private $redirectUri;
    /** @var int */
    private $appId;
    /** @var string */
    private $scope;

    /**
     * @param int    $appId
     * @param string $redirectUri
     * @param string $scope
     */
    public function __construct($appId, $redirectUri, $scope)
    {
        $this->appId = $appId;
        $this->redirectUri = $redirectUri;
        $this->scope = $scope;
    }

    /**
     * @deprecated
     *
     * @return string
     */
    public function redirectUri()
    {
        return $this->redirectUri;
    }

    /**
     * @return string
     */
    public function loginDialogUrl()
    {
        return self::FACEBOOK_LOGIN_DIALOG_URL.'?'.http_build_query(array(
            'client_id' => $this->appId,
            'redirect_uri' => $this->redirectUri,
            'scope' => implode(', ', $this->scope),
        ));
    }
}
