<?php
namespace Jmondi\Restforce\RestClient;

use GuzzleHttp\Client as GuzzleClient;
use Jmondi\Restforce\RestClient\RestClientInterface;
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
