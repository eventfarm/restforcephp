<?php
namespace Jmondi\Restforce;

use GuzzleHttp\Psr7\Response;
use Jmondi\Restforce\RestClient\RestClientInterface;
use Jmondi\Restforce\SalesforceOauth\TokenRefreshCallbackInterface;
use Mockery;
use Psr\Http\Message\ResponseInterface;

class RestforceClientTest extends \PHPUnit_Framework_TestCase
{
    const ACCESS_TOKEN = 'placeholder';
    const REFRESH_TOKEN = 'refreshtoken';
    const INSTANCE_URI = 'https://na15.salesforce.com';
    const SALESFORCE_CLIENT_ID = 'salesforce_client_id';
    const SALESFORCE_CLIENT_SECRET = 'salesforce_client_secret';
    const SALESFORCE_CALLBACK = 'salesforce_callback_url';
    const RESOURCE_OWNER_ID = 'resource_owner_id_url';
    const JSON_RESPONSE = '{ "id": "001410000056Kf0AAE", "woo": "foo" }';

    public function testLimits()
    {
        $response = Mockery::mock(ResponseInterface::class);
        $response->shouldReceive('getStatusCode')
            ->andReturn(200);

        $response->shouldReceive('getBody')
            ->andReturn($response);

        $response->shouldReceive('__toString')
            ->andReturn(self::JSON_RESPONSE);

        $client = Mockery::mock(RestClientInterface::class);
        $client->shouldReceive('request')
            ->with('GET', 'limits', [])
            ->andReturn($response)
            ->once();

        $restforceClient = $this->getRestforceClient($client);

        $result = $restforceClient->limits();

        $this->assertEquals(json_decode(self::JSON_RESPONSE), $result);
    }

    public function testUserInfo()
    {
        $response = Mockery::mock(ResponseInterface::class);
        $response->shouldReceive('getStatusCode')
            ->andReturn(200);

        $response->shouldReceive('getBody')
            ->andReturn($response);

        $response->shouldReceive('__toString')
            ->andReturn(self::JSON_RESPONSE);

        $client = Mockery::mock(RestClientInterface::class);
        $client->shouldReceive('request')
            ->with('GET', 'resource_owner_id_url', [])
            ->andReturn($response)
            ->once();

        $restforceClient = $this->getRestforceClient($client);

        $result = $restforceClient->userInfo();

        $this->assertEquals(json_decode(self::JSON_RESPONSE), $result);
    }

    public function testQuery()
    {
        $response = Mockery::mock(ResponseInterface::class);
        $response->shouldReceive('getStatusCode')
            ->andReturn(200);

        $response->shouldReceive('getBody')
            ->andReturn($response);

        $response->shouldReceive('__toString')
            ->andReturn(self::JSON_RESPONSE);

        $client = Mockery::mock(RestClientInterface::class);
        $client->shouldReceive('request')
            ->with('GET', 'query?q=SELECT+name', [])
            ->andReturn($response)
            ->once();

        $restforceClient = $this->getRestforceClient($client);

        $result = $restforceClient->query('SELECT name');

        $this->assertEquals(json_decode(self::JSON_RESPONSE), $result);
    }

    public function testQueryAll()
    {
        $response = Mockery::mock(ResponseInterface::class);
        $response->shouldReceive('getStatusCode')
            ->andReturn(200);

        $response->shouldReceive('getBody')
            ->andReturn($response);

        $response->shouldReceive('__toString')
            ->andReturn(self::JSON_RESPONSE);

        $client = Mockery::mock(RestClientInterface::class);
        $client->shouldReceive('request')
            ->with('GET', 'queryAll?q=SELECT+name', [])
            ->andReturn($response)
            ->once();

        $restforceClient = $this->getRestforceClient($client);

        $result = $restforceClient->queryAll('SELECT name');

        $this->assertEquals(json_decode(self::JSON_RESPONSE), $result);
    }

    public function testExplain()
    {
        $response = Mockery::mock(ResponseInterface::class);
        $response->shouldReceive('getStatusCode')
            ->andReturn(200);

        $response->shouldReceive('getBody')
            ->andReturn($response);

        $response->shouldReceive('__toString')
            ->andReturn(self::JSON_RESPONSE);

        $client = Mockery::mock(RestClientInterface::class);
        $client->shouldReceive('request')
            ->with('GET', 'query?explain=SELECT+name', [])
            ->andReturn($response)
            ->once();

        $restforceClient = $this->getRestforceClient($client);

        $result = $restforceClient->explain('SELECT name');

        $this->assertEquals(json_decode(self::JSON_RESPONSE), $result);
    }

    public function testBasic()
    {
        $response = Mockery::mock(ResponseInterface::class);
        $response->shouldReceive('getStatusCode')
            ->andReturn(200);

        $response->shouldReceive('getBody')
            ->andReturn($response);

        $response->shouldReceive('__toString')
            ->andReturn(self::JSON_RESPONSE);

        $client = Mockery::mock(RestClientInterface::class);
        $client->shouldReceive('request')
            ->with('GET', 'sobjects/Account', [])
            ->andReturn($response)
            ->once();

        $restforceClient = $this->getRestforceClient($client);

        $result = $restforceClient->basic('Account');

        $this->assertEquals(json_decode(self::JSON_RESPONSE), $result);
    }

    public function testFindWithoutParams()
    {
        $response = Mockery::mock(ResponseInterface::class);
        $response->shouldReceive('getStatusCode')
            ->andReturn(200);

        $response->shouldReceive('getBody')
            ->andReturn($response);

        $response->shouldReceive('__toString')
            ->andReturn(self::JSON_RESPONSE);

        $client = Mockery::mock(RestClientInterface::class);
        $client->shouldReceive('request')
            ->with('GET', 'sobjects/Account/001410000056Kf0AAE', [])
            ->andReturn($response)
            ->once();

        $restforceClient = $this->getRestforceClient($client);

        $result = $restforceClient->find('Account', '001410000056Kf0AAE');

        $this->assertEquals(json_decode(self::JSON_RESPONSE), $result);
    }

    public function testFindWithParams()
    {
        $response = Mockery::mock(ResponseInterface::class);
        $response->shouldReceive('getStatusCode')
            ->andReturn(200);

        $response->shouldReceive('getBody')
            ->andReturn($response);

        $response->shouldReceive('__toString')
            ->andReturn(self::JSON_RESPONSE);

        $client = Mockery::mock(RestClientInterface::class);
        $client->shouldReceive('request')
            ->with(
                'GET',
                'sobjects/Account/001410000056Kf0AAE?Name=MyName&SomethingElse=MySomethingElse',
                []
            )
            ->andReturn($response)
            ->once();

        $restforceClient = $this->getRestforceClient($client);

        $result = $restforceClient->find('Account', '001410000056Kf0AAE', [
            'Name' => 'MyName',
            'SomethingElse' => 'MySomethingElse'
        ]);

        $this->assertEquals(json_decode(self::JSON_RESPONSE), $result);
    }

    public function testDescribe()
    {
        $response = Mockery::mock(ResponseInterface::class);
        $response->shouldReceive('getStatusCode')
            ->andReturn(200);

        $response->shouldReceive('getBody')
            ->andReturn($response);

        $response->shouldReceive('__toString')
            ->andReturn(self::JSON_RESPONSE);

        $client = Mockery::mock(RestClientInterface::class);
        $client->shouldReceive('request')
            ->with(
                'GET',
                'sobjects/Account/describe',
                []
            )
            ->andReturn($response)
            ->once();

        $restforceClient = $this->getRestforceClient($client);

        $result = $restforceClient->describe('Account');

        $this->assertEquals(json_decode(self::JSON_RESPONSE), $result);
    }

    public function testPicklistValues()
    {
        $response = Mockery::mock(ResponseInterface::class);
        $response->shouldReceive('getStatusCode')
            ->andReturn(200);

        $response->shouldReceive('getBody')
            ->andReturn($response);

        $response->shouldReceive('__toString')
            ->andReturn('{ "name": "Task", "fields": [{"name":"Type", "picklistValues": [{"label": "Call", "value": "Call"}]}] }');

        $client = Mockery::mock(RestClientInterface::class);
        $client->shouldReceive('request')
            ->with(
                'GET',
                'sobjects/Task/describe',
                []
            )
            ->andReturn($response)
            ->once();

        $restforceClient = $this->getRestforceClient($client);

        $restforceClient->picklistValues('Task', 'Type');
    }

    public function testCreate()
    {
        $response = Mockery::mock(ResponseInterface::class);
        $response->shouldReceive('getStatusCode')
            ->andReturn(200);

        $response->shouldReceive('getBody')
            ->andReturn($response);

        $response->shouldReceive('__toString')
            ->andReturn(self::JSON_RESPONSE);

        $client = Mockery::mock(RestClientInterface::class);
        $client->shouldReceive('request')
            ->with(
                'POST',
                'sobjects/Account',
                [
                    'headers' => [
                        'Content-Type' => 'application/json'
                    ],
                    'json' => [
                        'Name' => 'TestCreateNewAccount'
                    ]
                ]
            )
            ->andReturn($response)
            ->once();

        $restforceClient = $this->getRestforceClient($client);

        $result = $restforceClient->create('Account', [
            'Name' => 'TestCreateNewAccount'
        ]);

        $this->assertEquals('001410000056Kf0AAE', $result);
    }

    public function testUpdate()
    {
        $response = Mockery::mock(ResponseInterface::class);
        $response->shouldReceive('getStatusCode')
            ->andReturn(204);

        $response->shouldReceive('getBody')
            ->andReturn($response);

        $response->shouldReceive('__toString')
            ->andReturn(self::JSON_RESPONSE);

        $client = Mockery::mock(RestClientInterface::class);
        $client->shouldReceive('request')
            ->with(
                'PATCH',
                'sobjects/Account/001410000056Kf0AAE',
                [
                    'headers' => [
                        'Content-Type' => 'application/json'
                    ],
                    'json' => [
                        'Name' => 'My Updated Name'
                    ]
                ]
            )
            ->andReturn($response)
            ->once();

        $restforceClient = $this->getRestforceClient($client);

        $result = $restforceClient->update('Account', '001410000056Kf0AAE', [
            'Name' => 'My Updated Name'
        ]);

        $this->assertEquals(true, $result);
    }


    public function testDestroy()
    {
        $response = Mockery::mock(ResponseInterface::class);
        $response->shouldReceive('getStatusCode')
            ->andReturn(204);

        $response->shouldReceive('getBody')
            ->andReturn($response);

        $response->shouldReceive('__toString')
            ->andReturn(self::JSON_RESPONSE);

        $client = Mockery::mock(RestClientInterface::class);
        $client->shouldReceive('request')
            ->with(
                'DELETE',
                'sobjects/Account/001410000056Kf0AAE',
                []
            )
            ->andReturn($response)
            ->once();

        $restforceClient = $this->getRestforceClient($client);

        $result = $restforceClient->destroy('Account', '001410000056Kf0AAE');

        $this->assertEquals(true, $result);
    }


    private function getRestforceClient($client):RestforceClient
    {
        $tokenRefreshCallback = Mockery::mock(TokenRefreshCallbackInterface::class);

        $restforceClient = new RestforceClient(
            $client,
            self::INSTANCE_URI,
            self::RESOURCE_OWNER_ID,
            $tokenRefreshCallback
        );
        return $restforceClient;
    }
}
