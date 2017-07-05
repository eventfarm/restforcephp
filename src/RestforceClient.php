<?php
namespace EventFarm\Restforce;

use EventFarm\Restforce\Models\SalesforcePicklist;
use EventFarm\Restforce\Models\SalesforceSobjectFieldlist;
use EventFarm\Restforce\Oauth\AccessToken;
use EventFarm\Restforce\Oauth\SalesforceProviderInterface;
use EventFarm\Restforce\Oauth\StevenMaguireSalesforceProvider;
use EventFarm\Restforce\RestClient\GuzzleRestClient;
use EventFarm\Restforce\RestClient\RestClientInterface;
use EventFarm\Restforce\RestClient\RestforceClientException;
use EventFarm\Restforce\RestClient\SalesforceRestClient;
use Psr\Http\Message\ResponseInterface;
use stdClass;

class RestforceClient implements RestforceClientInterface
{
    /**
     * @var RestClientInterface
     */
    private $client;

    const DEFAULT_HOST = 'https://login.salesforce.com';
    const DEFAULT_API_VERSION = 'v37.0';
    const DEFAULT_MAX_RETRY_REQUESTS = 2;
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
        int $maxRetryRequests = self::DEFAULT_MAX_RETRY_REQUESTS
    ) {

        return new self(
            $restClient,
            $salesforceProvider,
            new AccessToken($accessToken, $refreshToken, $instanceUrl),
            $resourceOwnerUrl,
            $tokenRefreshObject,
            $apiVersion,
            $maxRetryRequests
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
        int $maxRetryRequests = self::DEFAULT_MAX_RETRY_REQUESTS,
        string $domain = self::DEFAULT_HOST
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
            $tokenRefreshObject,
            $apiVersion,
            $maxRetryRequests
        );
    }

    /**
     * RestforceClient constructor.
     * @param RestClientInterface $restClient
     * @param SalesforceProviderInterface $salesforceProvider
     * @param AccessToken $accessToken
     * @param string $resourceOwnerUrl
     * @param TokenRefreshInterface|null $tokenRefreshObject
     * @param string $apiVersion
     * @param int $maxRetryRequests
     */
    private function __construct(
        RestClientInterface $restClient,
        SalesforceProviderInterface $salesforceProvider,
        AccessToken $accessToken,
        string $resourceOwnerUrl,
        $tokenRefreshObject,
        string $apiVersion,
        int $maxRetryRequests
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

    public function query(string $queryString): \stdClass
    {
        $uri = 'query?q=' . urlencode($queryString);
        $response = $this->request('GET', $uri);

        $responseBody = $this->getBodyObjectFromResponse($response);

        $records = $responseBody->records;

        while (isset($responseBody->nextRecordsUrl) && $responseBody->nextRecordsUrl !== null) {
            $paginationResponse = $this->request('GET', $responseBody->nextRecordsUrl);
            $paginationData = $this->getBodyObjectFromResponse($paginationResponse);
            $records = array_merge_recursive($records, $paginationData->records);
            $responseBody->nextRecordsUrl = $paginationData->nextRecordsUrl ?? null;
        }

        $responseBody->records = $records;

        return $responseBody;
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

    public function fieldList(string $sobject)
    {
        $fieldList = $this->describe($sobject)->fields;
        $picklist = new SalesforceSobjectFieldlist($fieldList);
        return $picklist->extract();
    }

    public function limits():stdClass
    {
        $response = $this->request('GET', '/limits');
        return $this->getBodyObjectFromResponse($response);
    }

    /**
     * @param string $sobject
     * @param array $data
     * @return string | bool
     */
    public function create(string $sobject, array $data)
    {
        $uri = '/sobjects/' . $sobject;
        $response = $this->request('POST', $uri, [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'json' => $data,
        ]);

        return $response->getStatusCode() === 201 ?
            $this->getBodyObjectFromResponse($response)->id :
            false;
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


    private function request(string $method, string $uri, array $options = []): ResponseInterface
    {
        $url = $this->cleanRequestUrl($uri);
        return $this->client->request($method, $url, $options);
    }

    private function cleanRequestUrl(string $uri):string
    {
        if ($uri[0] === '/') {
            $uri = substr($uri, 1);
        }

        return $uri;
    }

    private function getBodyObjectFromResponse(ResponseInterface $request): stdClass
    {
        try {
            return (object) json_decode($request->getBody()->__toString());
        } catch (\Throwable $e) {
            throw RestforceClientException::invalidJsonResponse($e->getMessage());
        }
    }
}
