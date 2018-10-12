<?php
namespace EventFarm\Restforce\Rest;

class OAuthAccessToken
{
    /** @var string */
    private $tokenType;
    /** @var string */
    private $accessToken;
    /** @var string */
    private $instanceUrl;
    /** @var string */
    private $resourceOwnerUrl;
    /** @var null|string */
    private $refreshToken;
    /** @var int|null */
    private $expiresAt;

    public function __construct(
        string $tokenType,
        string $accessToken,
        string $instanceUrl,
        string $resourceOwnerUrl,
        string $refreshToken = null,
        int $expiresAt = null
    ) {
        $this->tokenType = $tokenType;
        $this->accessToken = $accessToken;
        $this->refreshToken = $refreshToken;
        $this->instanceUrl = $instanceUrl;
        $this->resourceOwnerUrl = $resourceOwnerUrl;
        $this->expiresAt = $expiresAt;
    }

    public function getTokenType()
    {
        return $this->tokenType;
    }

    public function getAccessToken()
    {
        return $this->accessToken;
    }

    public function getRefreshToken()
    {
        return $this->refreshToken;
    }

    public function getInstanceUrl()
    {
        return $this->instanceUrl;
    }

    public function getHeaderString()
    {
        return $this->tokenType . ' ' . $this->accessToken;
    }

    public function getResourceOwnerUrl()
    {
        return $this->resourceOwnerUrl;
    }

    public function getExpiresAt()
    {
        return $this->expiresAt;
    }

    public function isExpired()
    {
        if ($this->expiresAt === null) {
            return false;
        }

        return time() > $this->expiresAt;
    }
}
