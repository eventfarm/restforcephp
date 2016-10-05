<?php
namespace Jmondi\Restforce;

use Jmondi\Restforce\Models\SalesforcePicklist;
use Jmondi\Restforce\Oauth\AccessToken;
use Jmondi\Restforce\Oauth\SalesforceProviderInterface;
use Jmondi\Restforce\Oauth\StevenMaguireSalesforceProvider;
use Jmondi\Restforce\RestClient\GuzzleRestClient;
use Jmondi\Restforce\RestClient\RestClientInterface;
use Jmondi\Restforce\RestClient\SalesforceRestClient;
use Psr\Http\Message\ResponseInterface;
use stdClass;

class RestforceClient
{
    /**
     * @var RestClientInterface
     */
    private $client;

    const DEFAULT_HOST = 'login.salesforce.com';
    const DEFAULT_API_VERSION = 'v37.0';
    const DEFAULT_RETRY_MAX_REQUESTS = 2;
    const DEFAULT_TOKEN_REFRESH_OBJECT = null;

    public static function with(
        RestClientInterface $restClient,
        SalesforceProviderInterface $salesforceProvider,
        string $accessToken,
        string $refreshToken,
        string $instanceUrl,
        string $resourceOwnerUrl,
        TokenRefreshInterface $tokenRefreshObject = self::DEFAULT_TOKEN_REFRESH_OBJECT,
        $apiVersion = self::DEFAULT_API_VERSION,
        int $retryMaxRequests = self::DEFAULT_RETRY_MAX_REQUESTS
    ) {
    
        return new self(
            $restClient,
            $salesforceProvider,
            new AccessToken($accessToken, $refreshToken, $instanceUrl),
            $resourceOwnerUrl,
            $apiVersion,
            $retryMaxRequests,
            $tokenRefreshObject
        );
    }

    public static function withDefaults(
        string $accessToken,
        string $refreshToken,
        string $instanceUrl,
        string $resourceOwnerUrl,
        string $clientId,
        string $clientSecret,
        string $redirectUrl,
        TokenRefreshInterface $tokenRefreshObject = self::DEFAULT_TOKEN_REFRESH_OBJECT,
        string $apiVersion = self::DEFAULT_API_VERSION,
        string $domain = self::DEFAULT_HOST,
        int $retryMaxRequests = self::DEFAULT_RETRY_MAX_REQUESTS
    ) {
    
        $restClient = GuzzleRestClient::createClient();
        $salesforceProvider =
            StevenMaguireSalesforceProvider::createDefaultProvider(
                $clientId,
                $clientSecret,
                $redirectUrl,
                $domain
            );
        return new self(
            $restClient,
            $salesforceProvider,
            new AccessToken($accessToken, $refreshToken, $instanceUrl),
            $resourceOwnerUrl,
            $apiVersion,
            $domain,
            $retryMaxRequests,
            $tokenRefreshObject
        );
    }

    private function __construct(
        RestClientInterface $restClient,
        SalesforceProviderInterface $salesforceProvider,
        AccessToken $accessToken,
        string $resourceOwnerUrl,
        string $apiVersion,
        int $maxRetryRequests,
        TokenRefreshInterface $tokenRefreshObject = null
    ) {
        $this->client = new SalesforceRestClient(
            $restClient,
            $salesforceProvider,
            $accessToken,
            $resourceOwnerUrl,
            $tokenRefreshObject,
            $apiVersion,
            $maxRetryRequests
        );
    }

    public function userInfo():stdClass
    {
        $response = $this->request('GET', $this->client->getResourceOwnerUrl());
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

    public function find(string $sobject, string $sobjectId = null, array $fields = []):stdClass
    {
        $query = http_build_query($fields);
        $uri = '/sobjects/' . $sobject . '/' . $sobjectId;
        $uri .= empty($fields) ? '' : '?' . $query;
        $response = $this->request('GET', $uri);
        return $this->getBodyObjectFromResponse($response);
    }

    public function describe(string $sobject):stdClass
    {
        $uri = '/sobjects/' . $sobject . '/describe';
        $response = $this->request('GET', $uri);
        return $this->getBodyObjectFromResponse($response);
    }

    public function picklistValues(string $sobject, string $field)
    {
        $fieldList = $this->describe($sobject)->fields;
        $picklist = new SalesforcePicklist($fieldList, $field);
        return $picklist->extract();
    }

    public function limits():stdClass
    {
        $response = $this->request('GET', '/limits');
        return $this->getBodyObjectFromResponse($response);
    }

    public function create(string $sobject, array $data):string
    {
        $uri = '/sobjects/' . $sobject;
        $response = $this->request('POST', $uri, [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'json' => $data,
        ]);
        return $this->getBodyObjectFromResponse($response)->id;
    }

    public function update(string $sobject, string $sobjectId, array $data)
    {
        $uri = '/sobjects/' . $sobject . '/' . $sobjectId;
        $response = $this->request('PATCH', $uri, [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'json' => $data,
        ]);
        $success = $response->getStatusCode() === 204;
        return $success;
    }

    public function destroy(string $sobject, string $sobjectId):bool
    {
        $uri = '/sobjects/' . $sobject . '/' . $sobjectId;
        $response = $this->request('DELETE', $uri);
        $success = $response->getStatusCode() === 204;
        return $success;
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

    private function getBodyObjectFromResponse(ResponseInterface $request)
    {
        return (object) json_decode($request->getBody()->__toString());
    }
}
