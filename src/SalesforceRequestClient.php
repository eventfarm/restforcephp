<?php
namespace Jmondi\Restforce;

use GuzzleHttp\Client as GuzzleClient;
use Jmondi\Restforce\Oauth\AccessTokenInterface;
use Jmondi\Restforce\Oauth\SalesforceProviderInterface;
use Jmondi\Restforce\RestClient\RestClientInterface;
use Jmondi\Restforce\Oauth\RetryAuthorizationTokenFailedException;
use Psr\Http\Message\ResponseInterface;

class SalesforceRequestClient implements RestClientInterface
{
    /**
     * @var RestClientInterface
     */
    private $client;
    /**
     * @var SalesforceProviderInterface
     */
    private $salesforceProvider;
    /**
     * @var AccessTokenInterface
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
        SalesforceProviderInterface $salesforceProvider,
        AccessTokenInterface $accessToken,
        TokenRefreshCallbackInterface $tokenRefreshCallback = null,
        string $apiVersion,
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
