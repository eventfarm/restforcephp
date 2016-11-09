<?php
namespace EventFarm\Restforce\Oauth;

class AccessToken implements AccessTokenInterface
{
    /** @var string */
    private $accessToken;

    /** @var string */
    private $refreshToken;

    /** @var string*/
    private $instanceUrl;

    /** @var string */
    private $values;

    public function __construct(
        string $accessToken,
        string $refreshToken,
        string $instanceUrl,
        $values = null
    ) {
        $this->accessToken = $accessToken;
        $this->refreshToken = $refreshToken;
        $this->instanceUrl = $instanceUrl;
        $this->values = $values;
    }

    public function getToken(): string
    {
        return $this->accessToken;
    }

    public function getRefreshToken(): string
    {
        return $this->refreshToken;
    }

    public function getInstanceUrl(): string
    {
        return $this->instanceUrl;
    }

    /**
     * @return null|string
     */
    public function getValues()
    {
        return $this->values;
    }
}
