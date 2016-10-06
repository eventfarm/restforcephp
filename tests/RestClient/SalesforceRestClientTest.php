<?php
namespace Jmondi\Restforce\Tests\RestClient;

use Jmondi\Restforce\Oauth\AccessToken;
use Jmondi\Restforce\Oauth\RetryAuthorizationTokenFailedException;
use Jmondi\Restforce\Oauth\StevenMaguireSalesforceProvider;
use Jmondi\Restforce\RestClient\GuzzleRestClient;
use Jmondi\Restforce\RestClient\SalesforceRestClient;
use Jmondi\Restforce\TokenRefreshInterface;
use Mockery;
use Psr\Http\Message\ResponseInterface;

class SalesforceRestClientTest extends \PHPUnit_Framework_TestCase
{
    public function testExceptionIsThrownWhenClientRetriesMoreThanMaxRetry()
    {
        $restClient = Mockery::mock(GuzzleRestClient::class);
        $provider = Mockery::mock(StevenMaguireSalesforceProvider::class);
        $accessToken = Mockery::mock(AccessToken::class);
        $tokenRefreshCallback = Mockery::mock(TokenRefreshInterface::class);

        $tokenRefreshCallback->shouldReceive('tokenRefreshCallback');

        $provider->shouldReceive('getAccessToken')
            ->andReturn($accessToken);

        $accessToken->shouldReceive('getToken')
            ->andReturn('MOCKACCESSTOKEN');
        $accessToken->shouldReceive('getInstanceUrl')
            ->andReturn('salesforce.com');
        $accessToken->shouldReceive('getRefreshToken')
            ->andReturn('TOKENSDKLJLKJWEF');

        $failedResponse = Mockery::mock(ResponseInterface::class);
        $failedResponse->shouldReceive('getStatusCode')
            ->andReturn(401);

        $restClient->shouldReceive('request')
            ->andReturn($failedResponse)
            ->times(3);

        $maxRetry = 3;
        $apiVersion = 'v37.0';
        $resourceOwnerUrl = "myResourceOwnerUrl";

        $salesforceProvider = new SalesforceRestClient(
            $restClient,
            $provider,
            $accessToken,
            $resourceOwnerUrl,
            $apiVersion,
            $maxRetry,
            $tokenRefreshCallback
        );

        $this->expectException(RetryAuthorizationTokenFailedException::class);
        $salesforceProvider->request('GET', '/example/getExample', []);
    }

    public function testFailThenRetryAndSucceedBeforeMaxRetryLimit()
    {
        $restClient = Mockery::mock(GuzzleRestClient::class);
        $provider = Mockery::mock(StevenMaguireSalesforceProvider::class);
        $accessToken = Mockery::mock(AccessToken::class);
        $tokenRefreshCallback = Mockery::mock(TokenRefreshInterface::class);

        $tokenRefreshCallback->shouldReceive('tokenRefreshCallback');

        $provider->shouldReceive('getAccessToken')
            ->andReturn($accessToken);

        $accessToken->shouldReceive('getToken')
            ->andReturn('MOCKACCESSTOKEN');
        $accessToken->shouldReceive('getInstanceUrl')
            ->andReturn('salesforce.com');

        $accessToken->shouldReceive('getRefreshToken')
            ->andReturn('TOKENSDKLJLKJWEF');

        $failedResponse = Mockery::mock(ResponseInterface::class);
        $failedResponse->shouldReceive('getStatusCode')
                       ->andReturn(401);

        $successResponse = Mockery::mock(ResponseInterface::class);
        $successResponse->shouldReceive('getStatusCode')
                        ->andReturn(200);

        $restClient->shouldReceive('request')
                   ->andReturn($failedResponse)
                   ->times(2);

        $restClient->shouldReceive('request')
                   ->andReturn($successResponse)
                   ->once();

        $maxRetry = 3;
        $apiVersion = 'v37.0';
        $resourceOwnerUrl = 'myResourceOwnerUrl';

        $salesforceProvider = new SalesforceRestClient(
            $restClient,
            $provider,
            $accessToken,
            $resourceOwnerUrl,
            $apiVersion,
            $maxRetry,
            $tokenRefreshCallback
        );

        $response = $salesforceProvider->request('GET', '/example/getExample');

        $this->assertSame(200, $response->getStatusCode());
    }
}
