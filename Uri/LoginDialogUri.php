<?php

namespace Lucaszz\FacebookAuthenticationBundle\Uri;

class LoginDialogUri implements FacebookUri
{
    const FACEBOOK_LOGIN_DIALOG_URL = 'https://www.facebook.com/dialog/oauth';

    /** @var FacebookUri */
    private $redirectUri;
    /** @var string */
    private $appId;
    /** @var array */
    private $scope;

    /**
     * @param FacebookUri $redirectUri
     * @param int         $appId
     * @param array       $scope
     */
    public function __construct(FacebookUri $redirectUri, $appId, array $scope)
    {
        $this->redirectUri = $redirectUri;
        $this->appId = $appId;
        $this->scope = $scope;
    }

    /** {@inheritdoc} */
    public function get()
    {
        return self::FACEBOOK_LOGIN_DIALOG_URL.'?'.http_build_query(array(
            'client_id' => $this->appId,
            'redirect_uri' => $this->redirectUri->get(),
            'scope' => implode(', ', $this->scope),
        ));
    }
}
