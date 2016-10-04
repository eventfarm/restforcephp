<?php
namespace Jmondi\Restforce;


use Jmondi\Restforce\Models\SalesforcePicklist;
use Jmondi\Restforce\RestClient\RestClientInterface;
use Psr\Http\Message\ResponseInterface;
use stdClass;

class RestforceClient
{
    /**
     * @var RestClientInterface
     */
    private $client;

    public function __construct(
        RestClientInterface $client
    ) {
        $this->client = $client;
    }

    public function userInfo():stdClass
    {
        $response = $this->request('GET', $this->client->getResourceOwnerUrl());
        return $this->getBodyObjectFromResponse($response);
    }

    public function query(string $queryString):stdClass
    {
        $uri = 'query?q=' . urlencode($queryString);
        $response = $this->request('GET', $uri);
        return $this->getBodyObjectFromResponse($response);
    }

    public function queryAll(string $queryString):stdClass
    {
        $uri = 'queryAll?q=' . urlencode($queryString);
        $response = $this->request('GET', $uri);
        return $this->getBodyObjectFromResponse($response);
    }

    public function explain(string $explainString):stdClass
    {
        $uri = 'query?explain=' . urlencode($explainString);
        $response = $this->request('GET', $uri);
        return $this->getBodyObjectFromResponse($response);
    }

    public function find(string $sobject, string $sobjectId = null, array $fields = []):stdClass
    {
        $query = http_build_query($fields);
        $uri = '/sobjects/' . $sobject . '/' . $sobjectId;
        $uri .= empty($fields) ? '' : '?' . $query;
        $response = $this->request('GET', $uri);
        return $this->getBodyObjectFromResponse($response);
    }

    public function describe(string $sobject):stdClass
    {
        $uri = '/sobjects/' . $sobject . '/describe';
        $response = $this->request('GET', $uri);
        return $this->getBodyObjectFromResponse($response);
    }

    public function picklistValues(string $sobject, string $field)
    {
        $fieldList = $this->describe($sobject)->fields;
        $picklist = new SalesforcePicklist($fieldList, $field);
        return $picklist->extract();
    }

    public function limits():stdClass
    {
        $response = $this->request('GET', '/limits');
        return $this->getBodyObjectFromResponse($response);
    }

    public function create(string $sobject, array $data):string
    {
        $uri = '/sobjects/' . $sobject;
        $response = $this->request('POST', $uri, [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'json' => $data,
        ]);
        return $this->getBodyObjectFromResponse($response)->id;
    }

    public function update(string $sobject, string $sobjectId, array $data)
    {
        $uri = '/sobjects/' . $sobject . '/' . $sobjectId;
        $response = $this->request('PATCH', $uri, [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'json' => $data,
        ]);
        $success = $response->getStatusCode() === 204;
        return $success;
    }

    public function destroy(string $sobject, string $sobjectId):bool
    {
        $uri = '/sobjects/' . $sobject . '/' . $sobjectId;
        $response = $this->request('DELETE', $uri);
        $success = $response->getStatusCode() === 204;
        return $success;
    }


    private function request(string $method, string $uri, array $options = []):ResponseInterface
    {
        $url = $this->cleanRequestUrl($uri);
        $response = $this->client->request($method, $url, $options);
        return $response;
    }

    private function cleanRequestUrl(string $uri):string
    {
        if ($uri[0] === '/') {
            $uri = substr($uri, 1);
        }

        return $uri;
    }

    private function getBodyObjectFromResponse(ResponseInterface $request)
    {
        return (object) json_decode($request->getBody()->__toString());
    }
}
