<?php
namespace Jmondi\Restforce\Tests;

use Jmondi\Restforce\Oauth\SalesforceProviderInterface;
use Jmondi\Restforce\RestClient\RestClientInterface;
use Jmondi\Restforce\RestforceClient;
use Mockery;
use Psr\Http\Message\ResponseInterface;

class RestforceClientTest extends \PHPUnit_Framework_TestCase
{
    const API_VERSION = 'v37.0';
    const INSTANCE_URL = 'myInstanceUrl';
    const RESOURCE_OWNER_URL = 'http://myResourceOwnerUrl';

    public function testLimitsSendsCorrectRequest()
    {
        // Arrange
        $salesforceProvider = $this->getSalesforceProviderMock();
        $response = $this->getResponseMock();

        // Act/Assert
        $restforceClient = $this->getRestforceClientWithParameterAsserts(
            $response,
            $salesforceProvider,
            'GET',
            $this->getBaseUrl() . 'limits',
            $this->getAuthorizationHeader()
        );
        $restforceClient->limits();
    }

    public function testUserInfo()
    {
        // Arrange
        $salesforceProvider = $this->getSalesforceProviderMock();
        $response = $this->getResponseMock();

        // Act/Assert
        $restforceClient = $this->getRestforceClientWithParameterAsserts(
            $response,
            $salesforceProvider,
            'GET',
            self::RESOURCE_OWNER_URL,
            $this->getAuthorizationHeader()
        );
        $restforceClient->userInfo();
    }

    public function testQuerySendsCorrectRequest()
    {
        // Arrange
        $salesforceProvider = $this->getSalesforceProviderMock();
        $response = $this->getResponseMock();

        // Act/Assert
        $restforceClient = $this->getRestforceClientWithParameterAsserts(
            $response,
            $salesforceProvider,
            'GET',
            $this->getBaseUrl() . 'query?q=SELECT+name',
            $this->getAuthorizationHeader()
        );
        $restforceClient->query('SELECT name');
    }

    public function testQueryAllSendsCorrectRequest()
    {
        // Arrange
        $salesforceProvider = $this->getSalesforceProviderMock();
        $response = $this->getResponseMock();

        // Act/Assert
        $restforceClient = $this->getRestforceClientWithParameterAsserts(
            $response,
            $salesforceProvider,
            'GET',
            $this->getBaseUrl() . 'queryAll?q=SELECT+name',
            $this->getAuthorizationHeader()
        );
        $restforceClient->queryAll('SELECT name');
    }

    public function testExplainSendsCorrectRequest()
    {
        // Arrange
        $salesforceProvider = $this->getSalesforceProviderMock();
        $response = $this->getResponseMock();

        // Act/Assert
        $restforceClient = $this->getRestforceClientWithParameterAsserts(
            $response,
            $salesforceProvider,
            'GET',
            $this->getBaseUrl() . 'query?explain=SELECT+name',
            $this->getAuthorizationHeader()
        );
        $restforceClient->explain('SELECT name');
    }

    public function testFindWithoutParamsSendsCorrectRequest()
    {
        // Arrange
        $salesforceProvider = $this->getSalesforceProviderMock();
        $response = $this->getResponseMock();

        // Act/Assert
        $restforceClient = $this->getRestforceClientWithParameterAsserts(
            $response,
            $salesforceProvider,
            'GET',
            $this->getBaseUrl() . 'sobjects/Account/001410000056Kf0AAE',
            $this->getAuthorizationHeader()
        );
        $restforceClient->find('Account', '001410000056Kf0AAE');
    }

    public function testFindWithParamsSendsCorrectRequest()
    {
        // Arrange
        $salesforceProvider = $this->getSalesforceProviderMock();
        $response = $this->getResponseMock();

        // Act/Assert
        $restforceClient = $this->getRestforceClientWithParameterAsserts(
            $response,
            $salesforceProvider,
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
        // Arrange
        $salesforceProvider = $this->getSalesforceProviderMock();
        $response = $this->getResponseMock();

        // Act/Assert
        $restforceClient = $this->getRestforceClientWithParameterAsserts(
            $response,
            $salesforceProvider,
            'GET',
            $this->getBaseUrl() . 'sobjects/Account/describe',
            $this->getAuthorizationHeader()
        );
        $restforceClient->describe('Account');
    }

    public function testPicklistValuesSendsCorrectRequest()
    {
        // Arrange
        $salesforceProvider = $this->getSalesforceProviderMock();
        $response = $this->getResponseMock(
            '{ "name": "Task", "fields": [{"name":"Type", "picklistValues": [{"label": "Call", "value": "Call"}]}] }'
        );

        // Act/Assert
        $restforceClient = $this->getRestforceClientWithParameterAsserts(
            $response,
            $salesforceProvider,
            'GET',
            $this->getBaseUrl() . 'sobjects/Task/describe',
            $this->getAuthorizationHeader()
        );
        $restforceClient->picklistValues('Task', 'Type');
    }

    public function testCreateSendsCorrectRequest()
    {
        // Arrange
        $salesforceProvider = $this->getSalesforceProviderMock();
        $response = $this->getResponseMock(
            '{ "id": "001410000056Kf0AAE" }'
        );

        // Act/Assert
        $restforceClient = $this->getRestforceClientWithParameterAsserts(
            $response,
            $salesforceProvider,
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
            ]
        );
        $restforceClient->create('Account', [
            'Name' => 'TestCreateNewAccount'
        ]);
    }

    public function testCreateSuccessReturnsIdOfNewObject()
    {
        // Arrange
        $salesforceProvider = $this->getSalesforceProviderMock();
        $response = $this->getResponseMock(
            '{ "id": "001410000056Kf0AAE" }',
            201
        );

        // Act
        $restforceClient = $this->getRestforceClient(
            $response,
            $salesforceProvider
        );
        $result = $restforceClient->create('Account', [
            'Name' => 'TestCreateNewAccount'
        ]);

        // Assert
        $this->assertEquals('001410000056Kf0AAE', $result);
    }

    public function testCreateFailureReturnsFalse()
    {
        // Arrange
        $salesforceProvider = $this->getSalesforceProviderMock();
        $response = $this->getResponseMock(
            '',
            400
        );

        // Act
        $restforceClient = $this->getRestforceClient(
            $response,
            $salesforceProvider
        );
        $result = $restforceClient->create('Account', [
            'Name' => 'TestCreateNewAccount'
        ]);

        // Assert
        $this->assertFalse($result);
    }

    public function testUpdateSendsCorrectRequest()
    {
        // Arrange
        $salesforceProvider = $this->getSalesforceProviderMock();
        $response = $this->getResponseMock('', 204);

        // Act/Assert
        $restforceClient = $this->getRestforceClientWithParameterAsserts(
            $response,
            $salesforceProvider,
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

    public function testUpdateSuccessReturnsTrue()
    {
        // Arrange
        $salesforceProvider = $this->getSalesforceProviderMock();
        $response = $this->getResponseMock('', 204);

        // Act
        $restforceClient = $this->getRestforceClient(
            $response,
            $salesforceProvider
        );
        $result = $restforceClient->update('Account', '001410000056Kf0AAE', [
            'Name' => 'My Updated Name'
        ]);

        // Assert
        $this->assertTrue($result);
    }

    public function testUpdateFailureReturnsFalse()
    {
        // Arrange
        $salesforceProvider = $this->getSalesforceProviderMock();
        $response = $this->getResponseMock('', 400);

        // Act
        $restforceClient = $this->getRestforceClient(
            $response,
            $salesforceProvider
        );
        $result = $restforceClient->update('Account', '001410000056Kf0AAE', [
            'Name' => 'My Updated Name'
        ]);

        // Assert
        $this->assertFalse($result);
    }

    public function testDestroySendsCorrectRequest()
    {
        // Arrange
        $salesforceProvider = $this->getSalesforceProviderMock();
        $response = $this->getResponseMock('', 204);

        // Act/Assert
        $restforceClient = $this->getRestforceClientWithParameterAsserts(
            $response,
            $salesforceProvider,
            'DELETE',
            $this->getBaseUrl() . 'sobjects/Account/001410000056Kf0AAE',
            $this->getAuthorizationHeader()
        );
        $restforceClient->destroy('Account', '001410000056Kf0AAE');
    }

    public function testDestroySuccessReturnsTrue()
    {
        // Arrange
        $salesforceProvider = $this->getSalesforceProviderMock();
        $response = $this->getResponseMock('', 204);

        // Act
        $restforceClient = $this->getRestforceClient(
            $response,
            $salesforceProvider
        );
        $result = $restforceClient->destroy('Account', '001410000056Kf0AAE');

        // Assert
        $this->assertTrue($result);
    }

    public function testDestroyFailureReturnsFalse()
    {
        // Arrange
        $salesforceProvider = $this->getSalesforceProviderMock();
        $response = $this->getResponseMock('', 400);

        // Act
        $restforceClient = $this->getRestforceClient(
            $response,
            $salesforceProvider
        );
        $result = $restforceClient->destroy('Account', '001410000056Kf0AAE');

        // Assert
        $this->assertFalse($result);
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

    private function getSalesforceProviderMock()
    {
        return  Mockery::mock(SalesforceProviderInterface::class);
    }

    private function getResponseMock(
        string $responseString = '',
        int $responseCode = 200
    ) {
        $response = Mockery::mock(ResponseInterface::class);
        $response->shouldReceive('getStatusCode')
            ->andReturn($responseCode);

        $response->shouldReceive('getBody')
            ->andReturn($response);

        $response->shouldReceive('__toString')
            ->andReturn($responseString);

        return $response;
    }

    private function getRestforceClientWithParameterAsserts(
        ResponseInterface $response,
        SalesforceProviderInterface $salesforceProvider,
        string $method,
        string $endpoint,
        array $options
    ):RestforceClient {
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

    private function getRestforceClient(
        ResponseInterface $response,
        SalesforceProviderInterface $salesforceProvider
    ):RestforceClient {
        $restClient = Mockery::mock(RestClientInterface::class);
        $restClient->shouldReceive('request')
            ->andReturn($response)
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
