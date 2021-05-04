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
    public const USER_INFO_ENDPOINT = 'RESOURCE_OWNER';

    private const DEFAULT_API_VERSION = 'v38.0';
    private const DEFAULT_AUTH_URL = 'https://login.salesforce.com';
    private const DEFAULT_GUZZLE_URL = 'https://na1.salesforce.com';

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
    /** @var string */
    private $authUrl;
    /** @var string */
    private $guzzleUrl;
    /** @var OAuthRestClient|null */
    private $oAuthRestClient;

    public function __construct(
        string $clientId,
        string $clientSecret,
        ?OAuthAccessToken $accessToken = null,
        ?string $username = null,
        ?string $password = null,
        ?string $apiVersion = null,
        ?string $authUrl = null
    ) {
        if ($accessToken === null && $username === null && $password === null) {
            throw RestforceException::minimumRequiredFieldsNotMet();
        }

        if ($apiVersion === null) {
            $apiVersion = self::DEFAULT_API_VERSION;
        }

        if ($authUrl === null) {
            $authUrl = self::DEFAULT_AUTH_URL;
            $guzzleUrl = self::DEFAULT_GUZZLE_URL;
        }

        $this->apiVersion = $apiVersion;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->accessToken = $accessToken;
        $this->username = $username;
        $this->password = $password;
        $this->authUrl = $authUrl;
        $this->guzzleUrl = isset($guzzleUrl) ? $guzzleUrl : $authUrl;
    }

    public function create(string $sobjectType, array $data): ResponseInterface
    {
        $uri = 'sobjects/' . $sobjectType;

        return $this->getOAuthRestClient()->postJson($uri, $data);
    }

    public function update(string $sobjectType, string $sobjectId, array $data): ResponseInterface
    {
        $uri = 'sobjects/' . $sobjectType . '/' . $sobjectId;

        return $this->getOAuthRestClient()->patchJson($uri, $data);
    }

    public function describe(string $sobject): ResponseInterface
    {
        $uri = 'sobjects/' . $sobject . '/describe';

        return $this->getOAuthRestClient()->get($uri);
    }

    public function find(string $sobjectType, string $sobjectId, array $fields = []): ResponseInterface
    {
        $uri = 'sobjects/' . $sobjectType . '/' . $sobjectId;

        $queryParams = [];

        if (!empty($fields)) {
            $fieldsString = implode(',', $fields);
            $queryParams = ['fields' => $fieldsString];
        }

        return $this->getOAuthRestClient()->get($uri, $queryParams);
    }

    public function limits(): ResponseInterface
    {
        return $this->getOAuthRestClient()->get('/limits');
    }

    public function getNext(string $url): ResponseInterface
    {
        return $this->getOAuthRestClient()->get($url);
    }

    public function query(string $queryString): ResponseInterface
    {
        return $this->getOAuthRestClient()->get('query', [
            'q' => $queryString,
        ]);
    }

    public function refreshToken(): ?OAuthAccessToken
    {
        return $this->getOAuthRestClient()->refreshToken();
    }

    public function userInfo(): ResponseInterface
    {
        return $this->getOAuthRestClient()->get(self::USER_INFO_ENDPOINT);
    }

    private function getOAuthRestClient(): RestClientInterface
    {
        if ($this->oAuthRestClient === null) {
            $this->oAuthRestClient = new OAuthRestClient(
                new SalesforceRestClient(
                    new GuzzleRestClient($this->guzzleUrl),
                    $this->apiVersion
                ),
                new GuzzleRestClient($this->authUrl),
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
