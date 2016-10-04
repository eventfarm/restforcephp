<?php
namespace Jmondi\Restforce;

use Jmondi\Restforce\Oauth\AccessToken;
use Jmondi\Restforce\Oauth\SalesforceProvider;
use Jmondi\Restforce\RestClient\GuzzleRestClient;
use Jmondi\Restforce\Oauth\RetryAuthorizationTokenFailedException;
use Mockery;
use Psr\Http\Message\ResponseInterface;

class SalesforceProviderRestClientTest extends \PHPUnit_Framework_TestCase
{
    public function testExceptionIsThrownWhenClientRetriesMoreThanMaxRetry()
    {
        $restClient = Mockery::mock(GuzzleRestClient::class);
        $provider = Mockery::mock(SalesforceProvider::class);
        $accessToken = Mockery::mock(AccessToken::class);
        $tokenRefreshCallback = Mockery::mock(TokenRefreshCallbackInterface::class);

        $tokenRefreshCallback->shouldReceive('tokenRefreshCallback');

        $provider->shouldReceive('getAccessToken')
            ->andReturn($accessToken);

        $accessToken->shouldReceive('getToken')
            ->andReturn('MOCKACCESSTOKEN');

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
        $salesforceProvider = new SalesforceRequestClient(
            $restClient,
            $provider,
            $accessToken,
            $tokenRefreshCallback,
            $apiVersion,
            $maxRetry
        );

        $this->expectException(RetryAuthorizationTokenFailedException::class);
        $salesforceProvider->request('GET', '/example/getExample', []);
    }

    public function testFailThenRetryAndSucceedBeforeMaxRetryLimit()
    {
        $restClient = Mockery::mock(GuzzleRestClient::class);
        $provider = Mockery::mock(SalesforceProvider::class);
        $accessToken = Mockery::mock(AccessToken::class);
        $tokenRefreshCallback = Mockery::mock(TokenRefreshCallbackInterface::class);

        $tokenRefreshCallback->shouldReceive('tokenRefreshCallback');

        $provider->shouldReceive('getAccessToken')
            ->andReturn($accessToken);

        $accessToken->shouldReceive('getToken')
            ->andReturn('MOCKACCESSTOKEN');

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
        $salesforceProvider = new SalesforceRequestClient(
            $restClient,
            $provider,
            $accessToken,
            $tokenRefreshCallback,
            $apiVersion,
            $maxRetry
        );

        $response = $salesforceProvider->request('GET', '/example/getExample');

        $this->assertSame(200, $response->getStatusCode());
    }
}
