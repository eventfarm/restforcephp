<?php
namespace Jmondi\Restforce;

use GuzzleHttp\Psr7\Response;
use Jmondi\Restforce\Token\TokenRefreshCallbackInterface;
use Mockery as Mock;
use Psr\Http\Message\ResponseInterface;

class RestforceClientTest extends \PHPUnit_Framework_TestCase
{
    const ACCESS_TOKEN = 'placeholder';
    const REFRESH_TOKEN = 'refreshtoken';
    const INSTANCE_URI = 'https://na15.salesforce.com';
    const SALESFORCE_CLIENT_ID = 'salesforce_client_id';
    const SALESFORCE_CLIENT_SECRET = 'salesforce_client_secret';
    const SALESFORCE_CALLBACK = 'salesforce_callback_url';
    const RESOURCE_OWNER_ID = 'resource_owner_id';
    const JSON_RESPONSE = '{ "woo": "foo" }';

    public function testQuery()
    {
        $client = Mock::mock(RestClientInterface::class);
        $tokenRefreshCallback = Mock::mock(TokenRefreshCallbackInterface::class);
        $response = Mock::mock(ResponseInterface::class);

        $response->shouldReceive('getStatusCode')
                ->andReturn(200);

        $response->shouldReceive('getBody')
            ->andReturn($response);

        $response->shouldReceive('__toString')
            ->andReturn(self::JSON_RESPONSE);

        $client->shouldReceive('request')
                ->with('GET', 'query?q=SELECT+name', [])
                ->andReturn($response)
                ->once();

        $restforceClient = new RestforceClient(
                $client,
                self::ACCESS_TOKEN,
                self::REFRESH_TOKEN,
                self::INSTANCE_URI,
                self::SALESFORCE_CLIENT_ID,
                self::SALESFORCE_CLIENT_SECRET,
                self::SALESFORCE_CALLBACK,
                self::RESOURCE_OWNER_ID,
                $tokenRefreshCallback
        );

        $result = $restforceClient->query('SELECT name');

        $this->assertEquals(json_decode(self::JSON_RESPONSE), $result);
    }
}
