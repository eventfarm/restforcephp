<?php
namespace EventFarm\Restforce\Rest;

use Psr\Http\Message\ResponseInterface;

interface SalesforceRestClientInterface extends RestClientInterface
{
    public function setResourceOwnerUrl(string $resourceOwnerUrl);
    public function setBaseUriForRestClient(string $baseUri);

    public function get(
        string $path,
        array $queryParameters = [],
        array $headers = [],
        float $timeoutSeconds = null
    );

    public function post(
        string $path,
        array $formParameters = [],
        array $headers = [],
        float $timeoutSeconds = null
    );

    public function postJson(
        string $path,
        array $jsonArray = [],
        array $headers = [],
        float $timeoutSeconds = null
    );

    public function patchJson(
        string $path,
        array $jsonArray = [],
        array $headers = [],
        float $timeoutSeconds = null
    );
}
