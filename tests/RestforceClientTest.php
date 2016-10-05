<?php
namespace Jmondi\Restforce;

use GuzzleHttp\Psr7\Response;
use Jmondi\Restforce\Oauth\AccessTokenInterface;
use Jmondi\Restforce\Oauth\SalesforceProviderInterface;
use Jmondi\Restforce\RestClient\RestClientInterface;
use Jmondi\Restforce\TokenRefreshInterface;
use Mockery;
use Psr\Http\Message\ResponseInterface;

class RestforceClientTest extends \PHPUnit_Framework_TestCase
{
    public function testLimitsSendsCorrectRequest()
    {
        $restforceClient = $this->getRestforceClient(
            'GET',
            'limits',
            $this->getAuthorizationHeader()
        );

        $restforceClient->limits();
    }

    // TODO: Need to make rest client smarter
//    public function testUserInfo()
//    {
//        $restforceClient = $this->getRestforceClient(
//            'GET',
//            'myResourceOwnerUrl',
//            $this->getAuthorizationHeader()
//        );
//
//        $restforceClient->userInfo();
//    }

    public function testQuerySendsCorrectRequest()
    {
        $restforceClient = $this->getRestforceClient(
            'GET',
            'query?q=SELECT+name',
            $this->getAuthorizationHeader()
        );

        $restforceClient->query("SELECT name");
    }

    public function testQueryAllSendsCorrectRequest()
    {
        $restforceClient = $this->getRestforceClient(
            'GET',
            'queryAll?q=SELECT+name',
            $this->getAuthorizationHeader()
        );

        $restforceClient->queryAll('SELECT name');
    }

    public function testExplainSendsCorrectRequest()
    {
        $restforceClient = $this->getRestforceClient(
            'GET',
            'query?explain=SELECT+name',
            $this->getAuthorizationHeader()
        );

        $restforceClient->explain('SELECT name');
    }

    public function testFindWithoutParamsSendsCorrectRequest()
    {
        $restforceClient = $this->getRestforceClient(
            'GET',
            'sobjects/Account/001410000056Kf0AAE',
            $this->getAuthorizationHeader()
        );

        $restforceClient->find('Account', '001410000056Kf0AAE');
    }

    public function testFindWithParamsSendsCorrectRequest()
    {
        $restforceClient = $this->getRestforceClient(
            'GET',
            'sobjects/Account/001410000056Kf0AAE?Name=MyName&SomethingElse=MySomethingElse',
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
            'sobjects/Account/describe',
            $this->getAuthorizationHeader()
        );

        $restforceClient->describe('Account');
    }

    public function testPicklistValuesSendsCorrectRequest()
    {
        $restforceClient = $this->getRestforceClient(
            'GET',
            'sobjects/Task/describe',
            $this->getAuthorizationHeader(),
            '{ "name": "Task", "fields": [{"name":"Type", "picklistValues": [{"label": "Call", "value": "Call"}]}] }'
        );

        $restforceClient->picklistValues('Task', 'Type');
    }

    public function testCreateSendsCorrectRequest()
    {
        $restforceClient = $this->getRestforceClient(
            'POST',
            'sobjects/Account',
            [
                'headers' => [
                    'Authorization' => 'Bearer ' . "myAccessToken",
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
            'sobjects/Account/001410000056Kf0AAE',
            [
                'headers' => [
                    'Authorization' => 'Bearer ' . "myAccessToken",
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
            'sobjects/Account/001410000056Kf0AAE',
            $this->getAuthorizationHeader()
        );

        $restforceClient->destroy('Account', '001410000056Kf0AAE');
    }

    private function getAuthorizationHeader()
    {
        return [
            'headers' => [
                'Authorization' => 'Bearer ' . "myAccessToken"
            ]
        ];
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
            ->andReturnUsing(function($m, $e, $o) use($method, $endpoint, $options, $response) {
                $this->assertEquals($method, $m);
                $this->assertEquals('myInstanceUrl/services/data/v37.0/' . $endpoint, $e);
                $this->assertEquals($options, $o);
                return $response;
            });

        return RestforceClient::with(
            $restClient,
            $salesforceProvider,
            'myAccessToken',
            'myRefreshToken',
            'myInstanceUrl',
            'http://myResourceOwnerUrl'
        );
    }
}
