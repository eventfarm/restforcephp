<?php
namespace EventFarm\Restforce\Rest;

use EventFarm\Restforce\Restforce;
use Psr\Http\Message\ResponseInterface;

/**
 * Class SalesforceRestClient
 *
 * @package EventFarm\Restforce\Rest
 */
class SalesforceRestClient implements SalesforceRestClientInterface
{
    /** @var GuzzleRestClient */
    private $restClient;
    /** @var string */
    private $apiVersion;
    /** @var string */
    private $resourceOwnerUrl;

    /**
     * SalesforceRestClient constructor.
     *
     * @param GuzzleRestClient $restClient rest client
     * @param string           $apiVersion api version
     */
    public function __construct(GuzzleRestClient $restClient, string $apiVersion)
    {
        $this->restClient = $restClient;
        $this->apiVersion = $apiVersion;
    }

    /**
     * Set resource owner url
     *
     * @param string $resourceOwnerUrl resource owner url
     *
     * @return void
     */
    public function setResourceOwnerUrl(string $resourceOwnerUrl)
    {
        $this->resourceOwnerUrl = $resourceOwnerUrl;
    }

    /**
     * Set base uri for rest client
     *
     * @param string $baseUri base uri
     *
     * @return void
     */
    public function setBaseUriForRestClient(string $baseUri)
    {
        $this->restClient->setBaseUriForRestClient($baseUri);
    }

    /**
     * Get method
     *
     * @param string     $path            path
     * @param array      $queryParameters parameters
     * @param array      $headers         headers
     * @param float|null $timeoutSeconds  timeout
     *
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function get(
        string $path,
        array $queryParameters = [],
        array $headers = [],
        float $timeoutSeconds = null
    ) {
        return $this->restClient->get(
            $this->constructUrl($path),
            $queryParameters,
            $headers,
            $timeoutSeconds
        );
    }

    /**
     * Post method
     *
     * @param string     $path           path
     * @param array      $formParameters parameters
     * @param array      $headers        headers
     * @param float|null $timeoutSeconds timeout
     *
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function post(
        string $path,
        array $formParameters = [],
        array $headers = [],
        float $timeoutSeconds = null
    ) {
        return $this->restClient->post(
            $this->constructUrl($path),
            $formParameters,
            $headers,
            $timeoutSeconds
        );
    }

    /**
     * Post method JSON formatted
     *
     * @param string     $path           path
     * @param array      $jsonArray      parameters
     * @param array      $headers        headers
     * @param float|null $timeoutSeconds timeout
     *
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function postJson(
        string $path,
        array $jsonArray = [],
        array $headers = [],
        float $timeoutSeconds = null
    ) {
        return $this->restClient->postJson(
            $this->constructUrl($path),
            $jsonArray,
            $headers,
            $timeoutSeconds
        );
    }

    /**
     * Patch method JSON formatted
     *
     * @param string     $path           path
     * @param array      $jsonArray      parameters
     * @param array      $headers        headers
     * @param float|null $timeoutSeconds timeout
     *
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function patchJson(
        string $path,
        array $jsonArray = [],
        array $headers = [],
        float $timeoutSeconds = null
    ) {
        return $this->restClient->patchJson(
            $this->constructUrl($path),
            $jsonArray,
            $headers,
            $timeoutSeconds
        );
    }

    /**
     * Put method CSV formatted
     *
     * @param string     $path           path
     * @param string     $filePath       file path
     * @param array      $headers        headers
     * @param float|null $timeoutSeconds timeout
     *
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function putCsv(
        string $path,
        string $filePath,
        array $headers = [],
        float $timeoutSeconds = null
    )
    {
        return $this->restClient->putCsv(
            $this->constructUrl($path),
            $filePath,
            $headers,
            $timeoutSeconds
        );
    }

    /**
     * Construct url for salesforce
     *
     * @param string $endpoint endpoint
     *
     * @return bool|string
     */
    private function constructUrl(string $endpoint)
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
}
