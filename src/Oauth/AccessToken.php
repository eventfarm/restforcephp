<?php
namespace Jmondi\Restforce\Oauth;

use Stevenmaguire\OAuth2\Client\Token\AccessToken as StevenMaguireAccessToken;

class AccessToken implements AccessTokenInterface
{
    /**
     * @var string
     */
    private $instanceUrl;
    /**
     * @var StevenMaguireAccessToken
     */
    private $accessToken;

    /**
     * @param array $options An array of options returned by the service provider
     *     in the access token request. The `access_token` option is required.
     */
    public function __construct(StevenMaguireAccessToken $accessToken)
    {
        $this->accessToken = $accessToken;
    }

    public function getInstanceUrl():string
    {
        return $this->accessToken->getInstanceUrl();
    }

    public function getRefreshToken():string
    {
        return $this->accessToken->getRefreshToken();
    }

    public function getResourceOwnerId():string
    {
        return $this->accessToken->getResourceOwnerId();
    }

    public function getToken():string
    {
        return $this->accessToken->getToken();
    }
}
