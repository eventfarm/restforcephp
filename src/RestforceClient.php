<?php
namespace Jmondi\Restforce;

use Jmondi\Restforce\RestClient\RestClientInterface;
use Jmondi\Restforce\SalesforceOauth\TokenRefreshCallbackInterface;
use Psr\Http\Message\ResponseInterface;
use stdClass;

class RestforceClient
{
    /**
     * @var string
     */
    private $accessToken;
    /**
     * @var string
     */
    private $refreshToken;
    /**
     * @var string
     */
    private $host;
    /**
     * @var int
     */
    private $retryCount;
    /**
     * @var string
     */
    private $clientId;
    /**
     * @var string
     */
    private $clientSecret;
    /**
     * @var string
     */
    private $redirectURI;
    /**
     * @var TokenRefreshCallbackInterface
     */
    private $tokenRefreshObject;
    /**
     * @var string
     */
    private $resourceOwnerUrl;
    /**
     * @var RestClientInterface
     */
    private $client;
    /**
     * @var string
     */
    private $instanceUrl;
    /**
     * @var array
     */
    private $headerOptions;

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
    ) {
    
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
        $this->instanceUrl = $instanceUrl;
        $this->headerOptions = $headerOptions;
    }

    public function userInfo():stdClass
    {
        $response = $this->request('GET', $this->resourceOwnerUrl);
        return $this->getBodyObjectFromResponse($response);
    }

    public function query(string $queryString):stdClass
    {
        $uri = 'query?q=' . urlencode($queryString);
        $response = $this->request('GET', $uri);
        return $this->getBodyObjectFromResponse($response);
    }

    public function queryAll(string $queryString):stdClass
    {
        $uri = 'queryAll?q=' . urlencode($queryString);
        $response = $this->request('GET', $uri);
        return $this->getBodyObjectFromResponse($response);
    }

    public function explain(string $explainString):stdClass
    {
        $uri = 'query?explain=' . urlencode($explainString);
        $response = $this->request('GET', $uri);
        return $this->getBodyObjectFromResponse($response);
    }

    public function find(string $type, string $id, array $fields = []):stdClass
    {
        $query = http_build_query($fields);
        $uri = '/sobjects/' . $type . '/' . $id;
        $uri .= empty($fields) ? '' : '?' . $query;
        $response = $this->request('GET', $uri);
        return $this->getBodyObjectFromResponse($response);
    }

    public function describe(string $type):stdClass
    {
        $uri = '/sobjects/' . $type . '/describe';
        $response = $this->request('GET', $uri);
        return $this->getBodyObjectFromResponse($response);
    }

    public function limits():stdClass
    {
        $response = $this->request('GET', '/limits');
        return $this->getBodyObjectFromResponse($response);
    }

    public function create(string $type, array $data):string
    {
        $uri = '/sobjects/' . $type;
        $response = $this->request('POST', $uri, [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'json' => $data,
        ]);
        return $this->getBodyObjectFromResponse($response)->id;
    }

    public function update(string $type, string $id, array $data)
    {
        $uri = '/sobjects/' . $type . '/' . $id;
        $response = $this->request('PATCH', $uri, [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'json' => $data,
        ]);
        $success = $response->getStatusCode() === 204;
        return $success;
    }

    public function destroy(string $type, string $id):bool
    {
        $uri = '/sobjects/' . $type . '/' . $id;
        $response = $this->request('DELETE', $uri);
        $success = $response->getStatusCode() === 204;
        return $success;
    }


    private function request(string $method, string $uri, array $options = []):ResponseInterface
    {
        $url = $this->cleanRequestUrl($uri);
        $options = array_merge_recursive($this->headerOptions, $options);
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

    private function getBodyObjectFromResponse(ResponseInterface $request)
    {
        return (object) json_decode($request->getBody()->__toString());
    }
}
