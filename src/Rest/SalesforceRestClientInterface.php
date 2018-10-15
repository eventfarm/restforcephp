<?php
namespace EventFarm\Restforce\Rest;

use Psr\Http\Message\ResponseInterface;

/**
 * Interface SalesforceRestClientInterface
 *
 * @package EventFarm\Restforce\Rest
 */
interface SalesforceRestClientInterface extends RestClientInterface
{
    /**
     * Set resource owner url
     *
     * @param string $resourceOwnerUrl resource owner url
     *
     * @return mixed
     */
    public function setResourceOwnerUrl(string $resourceOwnerUrl);

    /**
     * Set base uri for rest client
     *
     * @param string $baseUri base uri
     *
     * @return mixed
     */
    public function setBaseUriForRestClient(string $baseUri);

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
    );

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
    );

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
    );

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
    );
}
