<?php
namespace EventFarm\Restforce;

use EventFarm\Restforce\Rest\GuzzleRestClient;
use EventFarm\Restforce\Rest\OAuthAccessToken;
use EventFarm\Restforce\Rest\OAuthRestClient;
use EventFarm\Restforce\Rest\RestClientInterface;
use EventFarm\Restforce\Rest\SalesforceRestClient;
use Psr\Http\Message\ResponseInterface;

class Restforce implements RestforceInterface
{
    const USER_INFO_ENDPOINT = 'RESOURCE_OWNER';
    const SALESFORCE_API_ENDPOINT = 'https://voyageprive--preprod.cs100.my.salesforce.com';
    const DEFAULT_API_VERSION = 'v41.0';

    /** @var string */
    private $clientId;
    /** @var string */
    private $clientSecret;
    /** @var null|string */
    private $username;
    /** @var null|string */
    private $password;
    /** @var OAuthAccessToken|null */
    private $accessToken;
    /** @var string */
    private $apiVersion;
    /** @var OAuthRestClient|null */
    private $oAuthRestClient;
    /** @var string */
    private $apiEndpoint;

    public function __construct(
        string $clientId,
        string $clientSecret,
        OAuthAccessToken $accessToken = null,
        string $username = null,
        string $password = null,
        string $apiVersion = null,
        string $apiEndpoint = null
    ) {
        if ($accessToken === null && $username === null && $password === null) {
            throw RestforceException::minimumRequiredFieldsNotMet();
        }

        if ($apiVersion === null) {
            $apiVersion = self::DEFAULT_API_VERSION;
        }

        if ($apiEndpoint == null) {
            $apiEndpoint = self::SALESFORCE_API_ENDPOINT;
        }

        $this->apiEndpoint = $apiEndpoint;
        $this->apiVersion = $apiVersion;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->accessToken = $accessToken;
        $this->username = $username;
        $this->password = $password;
    }

    public function create(string $sobjectType, array $data)
    {
        $uri = 'sobjects/' . $sobjectType;

        return $this->getOAuthRestClient()->postJson($uri, $data);
    }

    public function update(string $sobjectType, string $sobjectId, array $data)
    {
        $uri = 'sobjects/' . $sobjectType . '/' . $sobjectId;

        return $this->getOAuthRestClient()->patchJson($uri, $data);
    }

    public function describe(string $sobject)
    {
        $uri = 'sobjects/' . $sobject . '/describe';

        return $this->getOAuthRestClient()->get($uri);
    }

    public function find(string $sobjectType, string $sobjectId, array $fields = [])
    {
        $uri = 'sobjects/' . $sobjectType . '/' . $sobjectId;

        $queryParams = [];

        if (!empty($fields)) {
            $fieldsString = implode(',', $fields);
            $queryParams = ['fields' => $fieldsString];
        }

        return $this->getOAuthRestClient()->get($uri, $queryParams);
    }

    public function parameterizedSearch(
        string $sobjectType,
        string $search,
        array $fields = [],
        string $whereQuery = null
    ) {
        $uri = 'parameterizedSearch';

        return $this->getOAuthRestClient()->postJson($uri, [
            "q" => $search,
            "fields" => $fields,
            "sobjects" => [
                [
                    "name" => $sobjectType,
                    "where" => $whereQuery
                ]
            ]
        ]);
    }

    public function limits()
    {
        return $this->getOAuthRestClient()->get('/limits');
    }

    public function getNext(string $url)
    {
        return $this->getOAuthRestClient()->get($url);
    }

    public function query(string $queryString)
    {
        return $this->getOAuthRestClient()->get('query', [
            'q' => $queryString,
        ]);
    }

    public function userInfo()
    {
        return $this->getOAuthRestClient()->get(self::USER_INFO_ENDPOINT);
    }

    private function getOAuthRestClient()
    {
        if ($this->oAuthRestClient === null) {
            $this->oAuthRestClient = new OAuthRestClient(
                new SalesforceRestClient(
                    new GuzzleRestClient($this->apiEndpoint),
                    $this->apiVersion
                ),
                new GuzzleRestClient($this->apiEndpoint),
                $this->clientId,
                $this->clientSecret,
                $this->username,
                $this->password,
                $this->accessToken
            );
        }
        return $this->oAuthRestClient;
    }
}
