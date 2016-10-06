<?php
namespace Jmondi\Restforce\Oauth;

class AccessToken implements AccessTokenInterface
{
    /**
     * @var string
     */
    private $accessToken;
    /**
     * @var string
     */
    private $refreshToken;
    /**
     * @var string
     */
    private $instanceUrl;

    public function __construct(
        string $accessToken,
        string $refreshToken,
        string $instanceUrl
    ) {
    
        $this->accessToken = $accessToken;
        $this->refreshToken = $refreshToken;
        $this->instanceUrl = $instanceUrl;
    }

    public function getToken():string
    {
        return $this->accessToken;
    }

    public function getRefreshToken():string
    {
        return $this->refreshToken;
    }

    public function getInstanceUrl():string
    {
        return $this->instanceUrl;
    }
}
