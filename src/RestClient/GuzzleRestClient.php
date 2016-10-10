<?php
namespace EventFarm\Restforce\RestClient;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

class GuzzleRestClient implements RestClientInterface
{
    public static function createClient()
    {
        return new self(
            new Client(['http_errors' => false])
        );
    }

    /**
     * @var Client
     */
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function request(string $method, string $uri = '', array $options = []):ResponseInterface
    {
        return $this->client->request($method, $uri, $options);
    }
}
