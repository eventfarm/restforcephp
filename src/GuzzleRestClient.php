<?php
namespace Jmondi\Restforce;

use GuzzleHttp\Client as GuzzleClient;
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
