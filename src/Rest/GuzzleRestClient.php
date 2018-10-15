<?php

namespace EventFarm\Restforce\Rest;

use Psr\Http\Message\ResponseInterface;

/**
 * Class GuzzleRestClient
 *
 * @package EventFarm\Restforce\Rest
 */
class GuzzleRestClient implements RestClientInterface
{
    const DEFAULT_TIMEOUT_SECONDS = 20.0;

    /** @var \GuzzleHttp\Client */
    private $client;
    /** @var bool */
    private $enableDebugging;

    /**
     * GuzzleRestClient constructor.
     * @param string $baseUri         base uri
     * @param bool   $enableDebugging debugging status
     */
    public function __construct(
        string $baseUri,
        bool $enableDebugging = false
    ) {
        $this->enableDebugging = $enableDebugging;
        $this->setBaseUriForRestClient($baseUri);
    }

    /**
     * Set base uri for client
     *
     * @param string $baseUri base uri
     *
     * @return void
     */
    public function setBaseUriForRestClient(string $baseUri)
    {
        if (!$this->containsTrailingSlash($baseUri)) {
            $baseUri .= '/';
        }

        $config = [
            'base_uri' => $baseUri,
            'debug' => $this->enableDebugging
        ];

        $this->client = new \GuzzleHttp\Client($config);
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
     */
    public function get(
        string $path,
        array $queryParameters = [],
        array $headers = [],
        float $timeoutSeconds = null
    ) {
        return $this->client->request(
            'GET',
            $path,
            [
                'timeout' => $timeoutSeconds,
                'headers' => $headers,
                'query' => $queryParameters
            ]
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
     */
    public function post(
        string $path,
        array $formParameters = [],
        array $headers = [],
        float $timeoutSeconds = null
    ) {
        return $this->client->request(
            'POST',
            $path,
            [
                'timeout' => $timeoutSeconds,
                'headers' => $headers,
                'form_params' => $formParameters
            ]
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
     */
    public function postJson(
        string $path,
        array $jsonArray = [],
        array $headers = [],
        float $timeoutSeconds = null
    ) {
        $headers['Content-Type'] = 'application/json';
        return $this->client->request(
            'POST',
            $path,
            [
                'timeout' => $timeoutSeconds,
                'headers' => $headers,
                'json' => $jsonArray
            ]
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
     */
    public function patchJson(
        string $path,
        array $jsonArray = [],
        array $headers = [],
        float $timeoutSeconds = null
    ) {
        $headers['Content-Type'] = 'application/json';

        return $this->client->request(
            'PATCH',
            $path,
            [
                'timeout' => $timeoutSeconds,
                'headers' => $headers,
                'json' => $jsonArray
            ]
        );
    }

    /**
     * Check if contains trailing slash
     *
     * @param string $baseUri base uri
     *
     * @return bool
     */
    private function containsTrailingSlash(string $baseUri)
    {
        return substr($baseUri, -1) === '/';
    }
}
