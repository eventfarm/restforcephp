<?php
namespace EventFarm\Restforce\Rest;

use EventFarm\Restforce\Restforce;
use Psr\Http\Message\ResponseInterface;

class SalesforceRestClient implements SalesforceRestClientInterface
{
    /** @var GuzzleRestClient */
    private $restClient;
    /** @var string */
    private $apiVersion;
    /** @var string */
    private $resourceOwnerUrl;

    public function __construct(GuzzleRestClient $restClient, string $apiVersion)
    {
        $this->restClient = $restClient;
        $this->apiVersion = $apiVersion;
    }

    public function setResourceOwnerUrl(string $resourceOwnerUrl): void
    {
        $this->resourceOwnerUrl = $resourceOwnerUrl;
    }

    public function setBaseUriForRestClient(string $baseUri): void
    {
        $this->restClient->setBaseUriForRestClient($baseUri);
    }

    public function get(
        string $path,
        array $queryParameters = [],
        array $headers = [],
        ?float $timeoutSeconds = null
    ): ResponseInterface {
        return $this->restClient->get(
            $this->constructUrl($path),
            $queryParameters,
            $headers,
            $timeoutSeconds
        );
    }

    public function post(
        string $path,
        array $formParameters = [],
        array $headers = [],
        ?float $timeoutSeconds = null
    ): ResponseInterface {
        return $this->restClient->post(
            $this->constructUrl($path),
            $formParameters,
            $headers,
            $timeoutSeconds
        );
    }

    public function postJson(
        string $path,
        array $jsonArray = [],
        array $headers = [],
        ?float $timeoutSeconds = null
    ): ResponseInterface {
        return $this->restClient->postJson(
            $this->constructUrl($path),
            $jsonArray,
            $headers,
            $timeoutSeconds
        );
    }

    public function patchJson(
        string $path,
        array $jsonArray = [],
        array $headers = [],
        ?float $timeoutSeconds = null
    ): ResponseInterface {
        return $this->restClient->patchJson(
            $this->constructUrl($path),
            $jsonArray,
            $headers,
            $timeoutSeconds
        );
    }

    private function constructUrl(string $endpoint): string
    {
        if ($endpoint === Restforce::USER_INFO_ENDPOINT) {
            return $this->resourceOwnerUrl;
        }

        if ((substr($endpoint, 0, 4) === 'http')) {
            return $endpoint;
        }

        if ((substr($endpoint, 0, 1) === '/')) {
            $endpoint = substr($endpoint, 1);
        }

        if (strpos($endpoint, 'services/data') !== false) {
            return '/' . $endpoint;
        }

        return '/services/data/' . $this->apiVersion . '/' . $endpoint;
    }

    public function refreshToken(): ?OAuthAccessToken
    {
        return null;
    }
}
