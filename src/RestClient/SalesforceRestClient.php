<?php
namespace Jmondi\Restforce\RestClient;

use Jmondi\Restforce\Oauth\AccessToken;
use Jmondi\Restforce\Oauth\SalesforceProviderInterface;
use Jmondi\Restforce\Oauth\RetryAuthorizationTokenFailedException;
use Jmondi\Restforce\TokenRefreshInterface;
use Psr\Http\Message\ResponseInterface;

//use Psr\Http\Message\ResponseInterface;

class SalesforceRestClient
{
    /**
     * @var RestClientInterface
     */
    private $restClient;
    /**
     * @var SalesforceProviderInterface
     */
    private $salesforceProvider;
    /**
     * @var AccessToken
     */
    private $accessToken;
    /**
     * @var string
     */
    private $resourceOwnerUrl;
    /**
     * @var TokenRefreshInterface|null
     */
    private $tokenRefreshCallback;
    /**
     * @var int
     */
    private $maxRetryRequests;
    /**
     * @var string
     */
    private $apiVersion;

    public function __construct(
        RestClientInterface $restClient,
        SalesforceProviderInterface $salesforceProvider,
        AccessToken $accessToken,
        string $resourceOwnerUrl,
        TokenRefreshInterface $tokenRefreshCallback = null,
        string $apiVersion = 'v37.0',
        int $maxRetryRequests = 2
    ) {
        $this->restClient = $restClient;
        $this->salesforceProvider = $salesforceProvider;
        $this->accessToken = $accessToken;
        $this->resourceOwnerUrl = $resourceOwnerUrl;
        $this->tokenRefreshCallback = $tokenRefreshCallback;
        $this->maxRetryRequests = $maxRetryRequests;
        $this->apiVersion = $apiVersion;
    }

    public function request(string $method, string $uri = '', array $options = []):ResponseInterface
    {
        return $this->retryRequest(
            $method,
            $this->constructUrl($uri),
            $this->mergeOptions($options)
        );
    }

    public function getResourceOwnerUrl():string
    {
        return $this->resourceOwnerUrl;
    }

    private function getAccessToken():string
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
            $response = $this->restClient->request($method, $uri, $options);
            $success = $this->isResponseAuthorized($response);

            if (!$success) {
                $this->refreshAccessToken();
            }

            $attempts++;
        } while (!$success && $attempts < $this->maxRetryRequests);

        if (!$success) {
            throw new RetryAuthorizationTokenFailedException(
                'Max retry limit of ' . $this->maxRetryRequests . 'has been reached. oAuth Token Failed.'
            );
        }

        return $response;
    }

    private function constructUrl(string $endpoint):string
    {
        $beginsWithHttp = (substr($endpoint, 0, 7) === "http://") || (substr($endpoint, 0, 8) === "https://");

        if ($beginsWithHttp) {
            return $endpoint;
        }

        $baseUrl = $this->accessToken->getInstanceUrl() . '/services/data/' . $this->apiVersion . '/';
        return $baseUrl . $endpoint;
    }
}
