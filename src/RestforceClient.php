<?php
namespace EventFarm\Restforce;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\StreamInterface;
use EventFarm\Restforce\Token\TokenRefreshCallbackInterface;
use Stevenmaguire\OAuth2\Client\Provider\Salesforce;
use Stevenmaguire\OAuth2\Client\Token\AccessToken;

class RestforceClient
{
    private $accessToken;
    private $refreshToken;
    private $host;
    private $retryCount;
    private $clientId;
    private $clientSecret;
    private $redirectURI;
    private $tokenRefreshObject;

    public function __construct(
        string $accessToken,
        string $refreshToken,
        string $instanceUrl,
        string $clientId,
        string $clientSecret,
        string $redirectURI,
        TokenRefreshCallbackInterface $tokenRefreshObject = null,
        string $apiVersion = 'v37.0',
        string $host = 'login.salesforce.com',
        int $retryCount = 4
    )
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->redirectURI = $redirectURI;
        $this->tokenRefreshObject = $tokenRefreshObject;
        $this->accessToken = $accessToken;
        $this->refreshToken = $refreshToken;
        $this->host = $host;
        $this->retryCount = $retryCount;
        $this->baseUrl = $instanceUrl . '/services/data/' . $apiVersion . '/';
    }

    public function request(string $method, string $uri, array $options = [])
    {
        $client = new GuzzleClient();
        $defaultOptions = [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->accessToken
            ]
        ];
        $overrideOptions = array_merge($defaultOptions, $options);

        // Remove leading '/' if it happens to be included.
        if (strpos($uri, '/') === 0) {
            $uri = substr($uri, 1);
        }
        $fullUrl = $this->baseUrl . $uri;

        $count = 0;
        do {
            try {
                $response = $client->request($method, $fullUrl, $overrideOptions);
            } catch (RequestException $e) {
                $success = $this->isResponseAuthorized($e->getCode());
            }
            $count += 1;
        } while(!$success || $count > $this->retryCount);

        return $response->getBody();
    }

    public function query(string $queryString):StreamInterface
    {
        $uri = 'query?q=' . urlencode($queryString);
        return $this->request('GET', $uri);
    }

    public function find(string $type, string $id):StreamInterface
    {
        $uri = '/sobjects/' . $type . '/' . $id;
        return $this->request('GET', $uri);
    }

    public function userInfo():StreamInterface
    {
        $uri = '/sobjects/Account';
        return $this->request('GET', $uri);
    }

    private function isResponseAuthorized(int $statusCode):bool
    {
        if ($statusCode === 401) {
            $this->refreshAccessToken();
            return false;
        } else {
            return true;
        }
    }

    private function refreshAccessToken():AccessToken
    {
        $salesforce = new Salesforce([
            'clientId' => $this->clientId,
            'clientSecret' => $this->clientSecret,
            'redirectUri' => $this->redirectURI,
        ]);

        $accessToken = $salesforce->getAccessToken('refresh_token', [
            'refresh_token' => $this->refreshToken
        ]);

        if (!empty($this->tokenRefreshObject)) {
            $this->tokenRefreshObject->tokenRefreshCallback($accessToken);
        }

        return $accessToken;
    }
}
