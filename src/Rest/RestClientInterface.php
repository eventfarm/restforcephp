<?php
namespace EventFarm\Restforce\Rest;

use Psr\Http\Message\ResponseInterface;

interface RestClientInterface
{
    public function get(
        string $path,
        array $queryParameters = [],
        array $headers = [],
        ?float $timeoutSeconds = null
    ): ResponseInterface;

    public function post(
        string $path,
        array $formParameters = [],
        array $headers = [],
        ?float $timeoutSeconds = null
    ): ResponseInterface;

    public function postJson(
        string $path,
        array $jsonArray = [],
        array $headers = [],
        ?float $timeoutSeconds = null
    ): ResponseInterface;

    public function patchJson(
        string $path,
        array $jsonArray = [],
        array $headers = [],
        ?float $timeoutSeconds = null
    ): ResponseInterface;

    public function refreshToken(): ?OAuthAccessToken;
}
