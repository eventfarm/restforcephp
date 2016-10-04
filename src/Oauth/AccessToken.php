<?php
namespace Jmondi\Restforce\Oauth;

class AccessToken implements AccessTokenInterface
{
    /**
     * @var string
     */
    private $instanceUrl;
    /**
     * @var AccessTokenInterface
     */
    private $accessToken;

    /**
     * @param AccessTokenInterface $accessToken
     */
    public function __construct($accessToken)
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
