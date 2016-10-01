<?php
namespace Jmondi\Restforce\Http;

use Psr\Http\Message\ResponseInterface;

interface RestClientInterface
{
    public function request(string $method, string $uri = '', array $options = []): ResponseInterface;
}
