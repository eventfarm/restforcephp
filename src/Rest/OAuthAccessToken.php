<?php
namespace EventFarm\Restforce\Rest;

class OAuthAccessToken
{
    /** @var string */
    private $tokenType;

    /** @var string */
    private $accessToken;

    /** @var int|null */
    private $expiresAt;

    /** @var string|null */
    private $refreshToken;

    /** @var string|null */
    private $userId;

    public function __construct(
        string $tokenType,
        string $accessToken,
        ?int $expiresAt = null,
        ?string $refreshToken = null,
        ?string $userId = null
    ) {
        $this->tokenType = $tokenType;
        $this->accessToken = $accessToken;
        $this->expiresAt = $expiresAt;
        $this->refreshToken = $refreshToken;
        $this->userId = $userId;
    }

    public function getTokenType(): string
    {
        return $this->tokenType;
    }

    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    public function getExpiresAt(): ?int
    {
        return $this->expiresAt;
    }

    public function getRefreshToken(): ?string
    {
        return $this->refreshToken;
    }

    public function getUserId(): ?string
    {
        return $this->userId;
    }

    public function getHeaderString(): string
    {
        return $this->tokenType . ' ' . $this->accessToken;
    }

    public function isExpired(): bool
    {
        return time() > $this->expiresAt;
    }
}
