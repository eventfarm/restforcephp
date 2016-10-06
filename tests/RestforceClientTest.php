<?php
namespace Jmondi\Restforce;

use Jmondi\Restforce\Oauth\SalesforceProviderInterface;
use Jmondi\Restforce\RestClient\RestClientInterface;
use Mockery;
use Psr\Http\Message\ResponseInterface;

class RestforceClientTest extends \PHPUnit_Framework_TestCase
{
    const API_VERSION = 'v37.0';
    const INSTANCE_URL = 'myInstanceUrl';
    const RESOURCE_OWNER_URL = 'http://myResourceOwnerUrl';

    public function testLimitsSendsCorrectRequest()
    {
        $restforceClient = $this->getRestforceClient(
            'GET',
            $this->getBaseUrl() . 'limits',
            $this->getAuthorizationHeader()
        );

        $restforceClient->limits();
    }

    public function testUserInfo()
    {
        $restforceClient = $this->getRestforceClient(
            'GET',
            self::RESOURCE_OWNER_URL,
            $this->getAuthorizationHeader()
        );

        $restforceClient->userInfo();
    }

    public function testQuerySendsCorrectRequest()
    {
        $restforceClient = $this->getRestforceClient(
            'GET',
            $this->getBaseUrl() . 'query?q=SELECT+name',
            $this->getAuthorizationHeader()
        );

        $restforceClient->query('SELECT name');
    }

    public function testQueryAllSendsCorrectRequest()
    {
        $restforceClient = $this->getRestforceClient(
            'GET',
            $this->getBaseUrl() . 'queryAll?q=SELECT+name',
            $this->getAuthorizationHeader()
        );

        $restforceClient->queryAll('SELECT name');
    }

    public function testExplainSendsCorrectRequest()
    {
        $restforceClient = $this->getRestforceClient(
            'GET',
            $this->getBaseUrl() . 'query?explain=SELECT+name',
            $this->getAuthorizationHeader()
        );

        $restforceClient->explain('SELECT name');
    }

    public function testFindWithoutParamsSendsCorrectRequest()
    {
        $restforceClient = $this->getRestforceClient(
            'GET',
            $this->getBaseUrl() . 'sobjects/Account/001410000056Kf0AAE',
            $this->getAuthorizationHeader()
        );

        $restforceClient->find('Account', '001410000056Kf0AAE');
    }

    public function testFindWithParamsSendsCorrectRequest()
    {
        $restforceClient = $this->getRestforceClient(
            'GET',
            $this->getBaseUrl() . 'sobjects/Account/001410000056Kf0AAE?Name=MyName&SomethingElse=MySomethingElse',
            $this->getAuthorizationHeader()
        );

        $restforceClient->find('Account', '001410000056Kf0AAE', [
            'Name' => 'MyName',
            'SomethingElse' => 'MySomethingElse'
        ]);
    }

    public function testDescribeSendsCorrectRequest()
    {
        $restforceClient = $this->getRestforceClient(
            'GET',
            $this->getBaseUrl() . 'sobjects/Account/describe',
            $this->getAuthorizationHeader()
        );

        $restforceClient->describe('Account');
    }

    public function testPicklistValuesSendsCorrectRequest()
    {
        $restforceClient = $this->getRestforceClient(
            'GET',
            $this->getBaseUrl() . 'sobjects/Task/describe',
            $this->getAuthorizationHeader(),
            '{ "name": "Task", "fields": [{"name":"Type", "picklistValues": [{"label": "Call", "value": "Call"}]}] }'
        );

        $restforceClient->picklistValues('Task', 'Type');
    }

    public function testCreateSendsCorrectRequest()
    {
        $restforceClient = $this->getRestforceClient(
            'POST',
            $this->getBaseUrl() . 'sobjects/Account',
            [
                'headers' => [
                    'Authorization' => 'Bearer ' . 'myAccessToken',
                    'Content-Type' => 'application/json'
                ],
                'json' => [
                    'Name' => 'TestCreateNewAccount'
                ]
            ],
            '{ "id": "001410000056Kf0AAE", "woo": "foo" }'
        );

        $restforceClient->create('Account', [
            'Name' => 'TestCreateNewAccount'
        ]);
    }

    public function testUpdateSendsCorrectRequest()
    {
        $restforceClient = $this->getRestforceClient(
            'PATCH',
            $this->getBaseUrl() . 'sobjects/Account/001410000056Kf0AAE',
            [
                'headers' => [
                    'Authorization' => 'Bearer ' . 'myAccessToken',
                    'Content-Type' => 'application/json'
                ],
                'json' => [
                    'Name' => 'My Updated Name'
                ]
            ]
        );

        $restforceClient->update('Account', '001410000056Kf0AAE', [
            'Name' => 'My Updated Name'
        ]);
    }


    public function testDestroySendsCorrectRequest()
    {
        $restforceClient = $this->getRestforceClient(
            'DELETE',
            $this->getBaseUrl() . 'sobjects/Account/001410000056Kf0AAE',
            $this->getAuthorizationHeader()
        );

        $restforceClient->destroy('Account', '001410000056Kf0AAE');
    }

    private function getAuthorizationHeader()
    {
        return [
            'headers' => [
                'Authorization' => 'Bearer ' . 'myAccessToken'
            ]
        ];
    }

    private function getBaseUrl():string
    {
        return self::INSTANCE_URL . '/services/data/' . self::API_VERSION . '/';
    }

    private function getRestforceClient(
        string $method,
        string $endpoint,
        array $options,
        string $responseString = ''
    ):RestforceClient {
        $salesforceProvider = Mockery::mock(SalesforceProviderInterface::class);

        $response = Mockery::mock(ResponseInterface::class);
        $response->shouldReceive('getStatusCode')
            ->andReturn(200);

        $response->shouldReceive('getBody')
            ->andReturn($response);

        $response->shouldReceive('__toString')
            ->andReturn($responseString);

        $restClient = Mockery::mock(RestClientInterface::class);
        $restClient->shouldReceive('request')
            ->andReturnUsing(function ($m, $e, $o) use ($method, $endpoint, $options, $response) {
                $this->assertEquals($method, $m);
                $this->assertEquals($endpoint, $e);
                $this->assertEquals($options, $o);
                return $response;
            })
            ->once();

        return RestforceClient::with(
            $restClient,
            $salesforceProvider,
            'myAccessToken',
            'myRefreshToken',
            self::INSTANCE_URL,
            self::RESOURCE_OWNER_URL
        );
    }
}
