<?php
namespace EventFarm\Restforce\Tests;

use EventFarm\Restforce\RestClient\RestClientInterface;
use Psr\Http\Message\ResponseInterface;

class FakeRestClient implements RestClientInterface
{
    /** @var ResponseInterface */
    private $response;

    public function __construct(ResponseInterface $response)
    {
        $this->response = $response;
    }

    public function request(string $method, string $uri = '', array $options = []): ResponseInterface
    {
        return $this->response;
    }
}
