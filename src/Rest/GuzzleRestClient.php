<?php
namespace EventFarm\Restforce\Rest;

use Psr\Http\Message\ResponseInterface;

final class GuzzleRestClient implements RestClientInterface
{
    const DEFAULT_TIMEOUT_SECONDS = 20.0;

    /** @var \GuzzleHttp\Client */
    private $client;

    public function __construct(
        string $baseUri,
        bool $enableDebugging = false
    ) {
        if (!$this->containsTrailingSlash($baseUri)) {
            $baseUri .= '/';
        }

        $config = [
            'base_uri' => $baseUri,
            'debug' => $enableDebugging
        ];

        $this->client = new \GuzzleHttp\Client($config);
    }

    public function get(
        string $path,
        array $queryParameters = [],
        array $headers = [],
        ?float $timeoutSeconds = null
    ): ResponseInterface {
        return $this->client->request(
            'GET',
            $path,
            [
                'timeout' => $timeoutSeconds,
                'headers' => $headers,
                'query' => $queryParameters,
                'http_errors' => false,
            ]
        );
    }

    public function post(
        string $path,
        array $formParameters = [],
        array $headers = [],
        ?float $timeoutSeconds = null
    ): ResponseInterface {
        return $this->client->request(
            'POST',
            $path,
            [
                'timeout' => $timeoutSeconds,
                'headers' => $headers,
                'form_params' => $formParameters,
                'http_errors' => false,
            ]
        );
    }
    /**
     * @param string $baseUri
     * @return bool
     */
    private function containsTrailingSlash($baseUri)
    {
        return substr($baseUri, -1) === '/';
    }
}
