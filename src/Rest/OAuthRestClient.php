<?php
namespace EventFarm\Restforce\Rest;

use Psr\Http\Message\ResponseInterface;
use Throwable;

final class OAuthRestClient implements RestClientInterface
{
    /** @var RestClientInterface */
    private $restClient;
    /** @var RestClientInterface */
    private $authRestClient;
    /** @var string */
    private $clientId;
    /** @var string */
    private $clientSecret;
    /** @var OAuthAccessToken|null */
    private $oAuthAccessToken;

    public function __construct(
        RestClientInterface $restClient,
        RestClientInterface $authRestClient,
        string $clientId,
        string $clientSecret,
        ?OAuthAccessToken $oAuthAccessToken = null
    ) {
        $this->restClient = $restClient;
        $this->authRestClient = $authRestClient;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->oAuthAccessToken = $oAuthAccessToken;
    }

    public function get(
        string $path,
        array $queryParameters = [],
        array $headers = [],
        ?float $timeoutSeconds = null
    ): ResponseInterface {
        return $this->restClient->get(
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
        return $this->restClient->get(
            $path,
            $formParameters,
            $this->getAuthorizationHeader($headers),
            $timeoutSeconds
        );
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


    /**
     * @return OAuthAccessToken
     */
    public function getOAuthAccessToken()
    {
        if ($this->oAuthAccessToken === null) {
            $this->oAuthAccessToken = $this->getClientCredentialsAccessToken();
        }

        if ($this->oAuthAccessToken->isExpired()) {
            $refreshToken = $this->oAuthAccessToken->getRefreshToken();

            if ($refreshToken !== null) {
                try {
                    $this->oAuthAccessToken = $this->getRefreshToken($refreshToken);
                } catch (OAuthRestClientException $e) {
                    $this->oAuthAccessToken = $this->getClientCredentialsAccessToken();
                }
            } else {
                $this->oAuthAccessToken = $this->getClientCredentialsAccessToken();
            }
        }

        return $this->oAuthAccessToken;
    }

    /**
     * @return OAuthAccessToken
     * @throws OAuthRestClientException
     */
    private function getClientCredentialsAccessToken(): OAuthAccessToken
    {
        $response = $this->authRestClient->post('/services/oauth2/token', [
            'grant_type' => 'client_credentials',
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
        ]);

        return $this->getOAuthAccessTokenFromResponse($response);
    }

    /**
     * @param string $refreshToken
     * @return OAuthAccessToken
     * @throws OAuthRestClientException
     */
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
    private function getOAuthAccessTokenFromResponse($response)
    {
        if ($response->getStatusCode() !== 200) {
            throw OAuthRestClientException::unableToLoadAccessToken();
        }

        $response = json_decode($response->getBody()->__toString(), true);

        try {
            return new OAuthAccessToken(
                $response['token_type'],
                $response['access_token'],
                $response['expires_at'],
                $response['refresh_token'],
                $response['user_id']
            );
        } catch (Throwable $e) {
            throw OAuthRestClientException::unableToLoadAccessToken();
        }
    }
}
