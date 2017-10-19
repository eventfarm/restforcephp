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
        ?string $refreshToken = null,
        ?int $expiresAt = null
    ) {
        $this->tokenType = $tokenType;
        $this->accessToken = $accessToken;
        $this->refreshToken = $refreshToken;
        $this->instanceUrl = $instanceUrl;
        $this->resourceOwnerUrl = $resourceOwnerUrl;
        $this->expiresAt = $expiresAt;
    }

    public function getTokenType(): string
    {
        return $this->tokenType;
    }

    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    public function getRefreshToken(): ?string
    {
        return $this->refreshToken;
    }

    public function getInstanceUrl(): string
    {
        return $this->instanceUrl;
    }

    public function getHeaderString(): string
    {
        return $this->tokenType . ' ' . $this->accessToken;
    }

    public function getResourceOwnerUrl(): string
    {
        return $this->resourceOwnerUrl;
    }

    public function getExpiresAt(): ?int
    {
        return $this->expiresAt;
    }

    public function isExpired(): bool
    {
        if ($this->expiresAt === null) {
            return false;
        }

        return time() > $this->expiresAt;
    }
}
