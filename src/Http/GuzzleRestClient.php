<?php
namespace Jmondi\Restforce\Http;

use GuzzleHttp\Client as GuzzleClient;
use Jmondi\Restforce\Http\RestClientInterface;
use Psr\Http\Message\ResponseInterface;

class GuzzleRestClient implements RestClientInterface
{

    /**
     * @var GuzzleClient
     */
    private $client;

    public function __construct(GuzzleClient $client)
    {

        $this->client = $client;
    }

    public function request(string $method, string $uri = '', array $options = []):ResponseInterface
    {
        return $this->client->request($method, $uri, $options);
    }
}
