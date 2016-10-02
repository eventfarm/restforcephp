<?php
namespace Jmondi\Restforce\SalesforceOauth;

use GuzzleHttp\Client as GuzzleClient;
use Jmondi\Restforce\RestClient\RestClientInterface;
use Jmondi\Restforce\SalesforceOauth\RetryAuthorizationTokenFailedException;
use Jmondi\Restforce\SalesforceOauth\TokenRefreshCallbackInterface;
use Psr\Http\Message\ResponseInterface;
use Stevenmaguire\OAuth2\Client\Provider\Salesforce as SalesforceProvider;
use Stevenmaguire\OAuth2\Client\Token\AccessToken;

class SalesforceProviderRestClient implements \Jmondi\Restforce\RestClient\RestClientInterface
{
    /**
     * @var GuzzleClient
     */
    private $client;
    /**
     * @var SalesforceProvider
     */
    private $salesforceProvider;
    /**
     * @var AccessToken
     */
    private $accessToken;
    /**
     * @var TokenRefreshCallbackInterface|null
     */
    private $tokenRefreshCallback;
    /**
     * @var int
     */
    private $maxRetry;

    public function __construct(
        \Jmondi\Restforce\RestClient\RestClientInterface $client,
        SalesforceProvider $salesforceProvider,
        AccessToken $accessToken,
        TokenRefreshCallbackInterface $tokenRefreshCallback = null,
        int $maxRetry = 2
    ) {
        $this->client = $client;
        $this->salesforceProvider = $salesforceProvider;
        $this->accessToken = $accessToken;
        $this->tokenRefreshCallback = $tokenRefreshCallback;
        $this->maxRetry = $maxRetry;
    }

    public function request(string $method, string $uri = '', array $options = []):ResponseInterface
    {
        return $this->retryRequest(
            $method,
            $uri,
            $this->mergeOptions($options)
        );
    }

    private function getAccessToken()
    {
        return $this->accessToken->getToken();
    }

    private function isResponseAuthorized(ResponseInterface $response):bool
    {
        return ! ($response->getStatusCode() === 401);
    }

    private function refreshAccessToken()
    {
        $refreshToken = $this->accessToken->getRefreshToken();

        $accessToken = $this->salesforceProvider->getAccessToken('refresh_token', [
            'refresh_token' => $refreshToken
        ]);

        if (!empty($this->tokenRefreshCallback)) {
            $this->tokenRefreshCallback->tokenRefreshCallback($accessToken);
        }

        $this->accessToken = $accessToken;
    }

    private function mergeOptions(array $options):array
    {
        $defaultOptions = [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->getAccessToken()
            ]
        ];
        $options = array_merge_recursive($defaultOptions, $options);
        return $options;
    }

    private function retryRequest(string $method, string $uri, array $options):ResponseInterface
    {
        $attempts = 0;
        do {
            $response = $this->client->request($method, $uri, $options);
            $success = $this->isResponseAuthorized($response);

            if (!$success) {
                $this->refreshAccessToken();
            }

            $attempts++;
        } while (!$success && $attempts < $this->maxRetry);

        if (!$success) {
            throw new RetryAuthorizationTokenFailedException(
                'Max retry limit of ' . $this->maxRetry . 'has been reached. oAuth Token Failed.'
            );
        }

        return $response;
    }
}
