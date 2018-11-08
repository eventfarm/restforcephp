<?php
namespace EventFarm\Restforce\Rest;

use Psr\Http\Message\ResponseInterface;
use Throwable;

/**
 * Class OAuthRestClient
 *
 * @package EventFarm\Restforce\Rest
 */
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

    /**
     * OAuthRestClient constructor.
     *
     * @param SalesforceRestClientInterface $apiRestClient    api rest client
     * @param RestClientInterface           $authRestClient   auth rest client
     * @param string                        $clientId         client id
     * @param string                        $clientSecret     client secret
     * @param string|null                   $username         username
     * @param string|null                   $password         password
     * @param OAuthAccessToken|null         $oAuthAccessToken access token
     */
    public function __construct(
        SalesforceRestClientInterface $apiRestClient,
        RestClientInterface $authRestClient,
        string $clientId,
        string $clientSecret,
        string $username = null,
        string $password = null,
        OAuthAccessToken $oAuthAccessToken = null
    ) {
        $this->apiRestClient = $apiRestClient;
        $this->authRestClient = $authRestClient;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->oAuthAccessToken = $oAuthAccessToken;
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * Get method
     *
     * @param string     $path            path
     * @param array      $queryParameters parameters
     * @param array      $headers         headers
     * @param float|null $timeoutSeconds  timeout
     *
     * @return mixed
     */
    public function get(
        string $path,
        array $queryParameters = [],
        array $headers = [],
        float $timeoutSeconds = null
    ) {
        $this->setParamsFromAccessToken();
        return $this->apiRestClient->get(
            $path,
            $queryParameters,
            $this->getAuthorizationHeader($headers),
            $timeoutSeconds
        );
    }

    /**
     * Post method
     *
     * @param string     $path           path
     * @param array      $formParameters parameters
     * @param array      $headers        headers
     * @param float|null $timeoutSeconds timeout
     *
     * @return mixed
     */
    public function post(
        string $path,
        array $formParameters = [],
        array $headers = [],
        float $timeoutSeconds = null
    ) {
        $this->setParamsFromAccessToken();
        return $this->apiRestClient->post(
            $path,
            $formParameters,
            $this->getAuthorizationHeader($headers),
            $timeoutSeconds
        );
    }

    /**
     * Post method JSON formatted
     *
     * @param string     $path           path
     * @param array      $jsonArray      parameters
     * @param array      $headers        headers
     * @param float|null $timeoutSeconds timeout
     *
     * @return mixed
     */
    public function postJson(
        string $path,
        array $jsonArray = [],
        array $headers = [],
        float $timeoutSeconds = null
    ) {
        $this->setParamsFromAccessToken();
        return $this->apiRestClient->postJson(
            $path,
            $jsonArray,
            $this->getAuthorizationHeader($headers),
            $timeoutSeconds
        );
    }

    /**
     * Patch method JSON formatted
     *
     * @param string     $path           path
     * @param array      $jsonArray      parameters
     * @param array      $headers        headers
     * @param float|null $timeoutSeconds timeout
     *
     * @return mixed
     */
    public function patchJson(
        string $path,
        array $jsonArray = [],
        array $headers = [],
        float $timeoutSeconds = null
    ) {
        $this->setParamsFromAccessToken();
        return $this->apiRestClient->patchJson(
            $path,
            $jsonArray,
            $this->getAuthorizationHeader($headers),
            $timeoutSeconds
        );
    }

    /**
     * Set params from access token
     *
     * @return void
     */
    private function setParamsFromAccessToken()
    {
        $this->apiRestClient->setBaseUriForRestClient($this->getOAuthAccessToken()->getInstanceUrl());
        $this->apiRestClient->setResourceOwnerUrl($this->getOAuthAccessToken()->getResourceOwnerUrl());
    }

    /**
     * Get OAuth access token
     *
     * @return OAuthAccessToken
     */
    private function getOAuthAccessToken()
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

    /**
     * Get authorization header
     *
     * @param array $headers headers
     *
     * @return array
     */
    private function getAuthorizationHeader(array $headers = [])
    {
        return array_merge(
            $headers,
            [
                'Authorization' => $this->getOAuthAccessToken()->getHeaderString(),
            ]
        );
    }

    /**
     * Get client credentials access token
     *
     * @return OAuthAccessToken
     */
    private function getClientCredentialsAccessToken()
    {
        $response = $this->authRestClient->post('/services/oauth2/token', [
            'grant_type' => 'client_credentials',
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
        ]);

        return $this->getOAuthAccessTokenFromResponse($response);
    }

    /**
     * Get password access token
     *
     * @return OAuthAccessToken
     */
    private function getPasswordAccessToken()
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

    /**
     * Get refresh token
     *
     * @param string $refreshToken refresh token
     *
     * @return OAuthAccessToken
     */
    private function getRefreshToken(string $refreshToken)
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
     * Get OAuth access token from response
     *
     * @param ResponseInterface $response response
     *
     * @return OAuthAccessToken
     * @throws OAuthRestClientException
     */
    private function getOAuthAccessTokenFromResponse(ResponseInterface $response)
    {
        if ($response->getStatusCode() !== 200) {
            throw OAuthRestClientException::unableToLoadAccessToken();
        }

        $response = json_decode($response->getBody(), true);

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

    /**
     * Get new token
     *
     * @return OAuthAccessToken
     */
    private function getNewToken()
    {
        if ($this->username === null && $this->password === null) {
            return $this->getClientCredentialsAccessToken();
        } else {
            return $this->getPasswordAccessToken();
        }
    }
}
