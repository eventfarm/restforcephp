<?php
namespace Jmondi\Restforce;

use Jmondi\Restforce\Token\TokenRefreshCallbackInterface;
use Psr\Http\Message\ResponseInterface;
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
    private $resourceOwnerUrl;
    private $client;

    public function __construct(
        RestClientInterface $client,
        string $accessToken,
        string $refreshToken,
        string $instanceUrl,
        string $clientId,
        string $clientSecret,
        string $redirectURI,
        string $resourceOwnerUrl,
        TokenRefreshCallbackInterface $tokenRefreshObject = null,
        array $headerOptions = [],
        string $host = 'login.salesforce.com',
        int $retryCount = 4
    )
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->redirectURI = $redirectURI;
        $this->resourceOwnerUrl = $resourceOwnerUrl;
        $this->tokenRefreshObject = $tokenRefreshObject;
        $this->accessToken = $accessToken;
        $this->refreshToken = $refreshToken;
        $this->host = $host;
        $this->retryCount = $retryCount;
        $this->client = $client;
    }

    public function userInfo():string
    {
        $request = $this->request('GET', $this->resourceOwnerUrl);
        return $request->getBody()->__toString();
    }

    public function query(string $queryString):string
    {
        $uri = 'query?q=' . urlencode($queryString);
        $response = $this->request('GET', $uri);
        return $response->getBody()->__toString();
    }

    public function find(string $type, string $id):string
    {
        $uri = '/sobjects/' . $type . '/' . $id;
        $request = $this->request('GET', $uri);
        return $request->getBody()->__toString();
    }

    public function limits():string
    {
        $request = $this->request('GET', '/limits');
        return $request->getBody()->__toString();

    }

    public function create(string $type, array $data):string
    {
        $uri = '/sobjects/' . $type;
        $request = $this->request('POST', $uri, [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'json' => $data,
        ]);
        return $request->getBody()->__toString();
    }

    public function update(string $type, string $id, array $data)
    {
        $uri = '/sobjects/' . $type . '/' . $id;
        $request = $this->request('PATCH', $uri, [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'json' => $data,
        ]);
//        if ($request->getStatusCode() > 299) {
            dd($request->getReasonPhrase());
//        }
    }

    private function request(string $method, string $uri, array $options = []):ResponseInterface
    {
        $url = $this->cleanRequestUrl($uri);
        $response = $this->client->request($method, $url, $options);
        return $response;
    }

    private function cleanRequestUrl(string $uri):string
    {
        if ($uri[0] === '/') {
            $uri = substr($uri, 1);
        }

        return $uri;
    }
//
//
//    private function getProvider():Salesforce
//    {
//        $salesforce = new Salesforce([
//            'clientId' => $this->clientId,
//            'clientSecret' => $this->clientSecret,
//            'redirectUri' => $this->redirectURI,
//        ]);
//        return $salesforce;
//    }
}
