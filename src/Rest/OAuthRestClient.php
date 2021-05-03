<?php
namespace EventFarm\Restforce\Rest;

use Psr\Http\Message\ResponseInterface;
use Throwable;

final class OAuthRestClient implements RestClientInterface
{
    const TOKEN_TYPE = 'Bearer';
    /** @var SalesforceRestClientInterface */
    private $apiRestClient;
    /** @var RestClientInterface */
    private $authRestClient;
    /** @var string */
    private $clientId;
    /** @var string */
    private $clientSecret;
    /** @var OAuthAccessToken|null */
    private $oAuthAccessToken;
    /** @var null|string */
    private $username;
    /** @var null|string */
    private $password;

    public function __construct(
        SalesforceRestClientInterface $apiRestClient,
        RestClientInterface $authRestClient,
        string $clientId,
        string $clientSecret,
        ?string $username = null,
        ?string $password = null,
        ?OAuthAccessToken $oAuthAccessToken = null
    ) {
        $this->apiRestClient = $apiRestClient;
        $this->authRestClient = $authRestClient;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->oAuthAccessToken = $oAuthAccessToken;
        $this->username = $username;
        $this->password = $password;
    }

    public function get(
        string $path,
        array $queryParameters = [],
        array $headers = [],
        ?float $timeoutSeconds = null
    ): ResponseInterface {
        $this->setParamsFromAccessToken();
        return $this->apiRestClient->get(
            $path,
            $queryParameters,
            $this->getAuthorizationHeader($headers),
            $timeoutSeconds
        );
    }

    public function post(
        string $path,
        array $formParameters = [],
        array $headers = [],
        ?float $timeoutSeconds = null
    ): ResponseInterface {
        $this->setParamsFromAccessToken();
        return $this->apiRestClient->post(
            $path,
            $formParameters,
            $this->getAuthorizationHeader($headers),
            $timeoutSeconds
        );
    }

    public function postJson(
        string $path,
        array $jsonArray = [],
        array $headers = [],
        ?float $timeoutSeconds = null
    ): ResponseInterface {
        $this->setParamsFromAccessToken();
        return $this->apiRestClient->postJson(
            $path,
            $jsonArray,
            $this->getAuthorizationHeader($headers),
            $timeoutSeconds
        );
    }

    public function patchJson(
        string $path,
        array $jsonArray = [],
        array $headers = [],
        ?float $timeoutSeconds = null
    ): ResponseInterface {
        $this->setParamsFromAccessToken();
        return $this->apiRestClient->patchJson(
            $path,
            $jsonArray,
            $this->getAuthorizationHeader($headers),
            $timeoutSeconds
        );
    }

    public function refreshToken(): ?OAuthAccessToken
    {
        $refreshToken = $this->oAuthAccessToken->getRefreshToken();
        $refreshedToken = $this->getRefreshToken($refreshToken);
        $this->oAuthAccessToken = $refreshedToken;
        return $refreshedToken;
    }


    private function setParamsFromAccessToken(): void
    {
        $this->apiRestClient->setBaseUriForRestClient($this->getOAuthAccessToken()->getInstanceUrl());
        $this->apiRestClient->setResourceOwnerUrl($this->getOAuthAccessToken()->getResourceOwnerUrl());
    }

    private function getOAuthAccessToken(): OAuthAccessToken
    {
        if ($this->oAuthAccessToken === null) {
            $this->oAuthAccessToken = $this->getNewToken();
        }

        if ($this->oAuthAccessToken->isExpired()) {
            $refreshToken = $this->oAuthAccessToken->getRefreshToken();

            if ($refreshToken !== null) {
                try {
                    $this->oAuthAccessToken = $this->getRefreshToken($refreshToken);
                } catch (OAuthRestClientException $e) {
                    $this->oAuthAccessToken = $this->getNewToken();
                }
            } else {
                $this->oAuthAccessToken = $this->getNewToken();
            }
        }

        return $this->oAuthAccessToken;
    }

    private function getAuthorizationHeader(array $headers = [])
    {
        return array_merge(
            $headers,
            [
                'Authorization' => $this->getOAuthAccessToken()->getHeaderString(),
            ]
        );
    }

    private function getClientCredentialsAccessToken(): OAuthAccessToken
    {
        $response = $this->authRestClient->post('/services/oauth2/token', [
            'grant_type' => 'client_credentials',
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
        ]);

        return $this->getOAuthAccessTokenFromResponse($response);
    }

    private function getPasswordAccessToken(): OAuthAccessToken
    {
        $response = $this->authRestClient->post('/services/oauth2/token', [
            'grant_type' => 'password',
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'username' => $this->username,
            'password' => $this->password
        ]);

        return $this->getOAuthAccessTokenFromResponse($response);
    }

    private function getRefreshToken(string $refreshToken): OAuthAccessToken
    {
        $response = $this->authRestClient->post('/services/oauth2/token', [
            'grant_type' => 'refresh_token',
            'refresh_token' => $refreshToken,
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
        ]);

        return $this->getOAuthAccessTokenFromResponse($response);
    }

    /**
     * @param ResponseInterface $response
     * @return OAuthAccessToken
     * @throws OAuthRestClientException
     */
    private function getOAuthAccessTokenFromResponse(ResponseInterface $response): OAuthAccessToken
    {
        if ($response->getStatusCode() !== 200) {
            $message = '(' . $response->getStatusCode() . ') ' . $response->getBody()->__toString();
            throw OAuthRestClientException::unableToLoadAccessToken($message);
        }

        $response = json_decode($response->getBody()->__toString(), true);

        try {
            $resourceOwnerUrl = $response['id'];

            return new OAuthAccessToken(
                self::TOKEN_TYPE,
                $response['access_token'],
                $response['instance_url'],
                $resourceOwnerUrl,
                $response['refresh_token'] ?? null,
                $response['expires_at'] ?? null
            );
        } catch (Throwable $e) {
            throw OAuthRestClientException::unableToLoadAccessToken();
        }
    }

    private function getNewToken(): OAuthAccessToken
    {
        if ($this->username === null && $this->password === null) {
            return $this->getClientCredentialsAccessToken();
        } else {
            return $this->getPasswordAccessToken();
        }
    }
}
