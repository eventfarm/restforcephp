<?php
namespace EventFarm\Restforce\Rest;

/**
 * Class OAuthAccessToken
 *
 * @package EventFarm\Restforce\Rest
 */
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

    /**
     * OAuthAccessToken constructor.
     *
     * @param string      $tokenType        token type (password, etc.)
     * @param string      $accessToken      access token
     * @param string      $instanceUrl      instance url
     * @param string      $resourceOwnerUrl resource owner url
     * @param string|null $refreshToken     refresh token
     * @param int|null    $expiresAt        expires value
     */
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

    /**
     * Get token type
     *
     * @return string
     */
    public function getTokenType()
    {
        return $this->tokenType;
    }

    /**
     * Get access token
     *
     * @return string
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * Get refresh token
     *
     * @return null|string
     */
    public function getRefreshToken()
    {
        return $this->refreshToken;
    }

    /**
     * Get instance url
     *
     * @return string
     */
    public function getInstanceUrl()
    {
        return $this->instanceUrl;
    }

    /**
     * Get header string
     *
     * @return string
     */
    public function getHeaderString()
    {
        return $this->tokenType . ' ' . $this->accessToken;
    }

    /**
     * Get resource owner url
     *
     * @return string
     */
    public function getResourceOwnerUrl()
    {
        return $this->resourceOwnerUrl;
    }

    /**
     * Get expires at value
     *
     * @return int|null
     */
    public function getExpiresAt()
    {
        return $this->expiresAt;
    }

    /**
     * Check if expired
     *
     * @return bool
     */
    public function isExpired()
    {
        if ($this->expiresAt === null) {
            return false;
        }

        return time() > $this->expiresAt;
    }
}
