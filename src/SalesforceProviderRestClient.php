<?php
namespace Jmondi\Restforce;

use GuzzleHttp\Client as GuzzleClient;
use Jmondi\Restforce\Token\TokenRefreshCallbackInterface;
use Psr\Http\Message\ResponseInterface;
use Stevenmaguire\OAuth2\Client\Provider\Salesforce as SalesforceProvider;
use Stevenmaguire\OAuth2\Client\Token\AccessToken;

class SalesforceProviderRestClient implements RestClientInterface
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
        RestClientInterface $client,
        SalesforceProvider $salesforceProvider,
        AccessToken $accessToken,
        TokenRefreshCallbackInterface $tokenRefreshCallback = null,
        int $maxRetry = 2
    )
    {
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
        if ($response->getStatusCode() === 401) {
            return false;
        } else {
            return true;
        }
    }

    private function refreshAccessToken():void
    {
        var_dump('refresh access token!! Dying on this to verify access token temp');
        die();
        $accessToken = $this->salesforceProvider->getAccessToken('refresh_token', [
            'refresh_token' => $this->accessToken->getRefreshToken()
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

        return $response;
    }
}