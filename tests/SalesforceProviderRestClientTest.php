<?php
namespace Jmondi\Restforce;

use Jmondi\Restforce\Http\RestClientInterface;
use Jmondi\Restforce\Http\RetryAuthorizationTokenFailedException;
use Jmondi\Restforce\Http\SalesforceProviderRestClient;
use Jmondi\Restforce\Token\TokenRefreshCallbackInterface;
use Mockery;
use Psr\Http\Message\ResponseInterface;
use Stevenmaguire\OAuth2\Client\Provider\Salesforce as SalesforceProvider;
use Stevenmaguire\OAuth2\Client\Token\AccessToken;

class SalesforceProviderRestClientTest extends \PHPUnit_Framework_TestCase
{
    public function testExceptionIsThrownWhenClientRetriesMoreThanMaxRetry()
    {
        $restClient = Mockery::mock(RestClientInterface::class);
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
        $salesforceProvider = new SalesforceProviderRestClient(
            $restClient,
            $provider,
            $accessToken,
            $tokenRefreshCallback,
            $maxRetry
        );

        $this->expectException(RetryAuthorizationTokenFailedException::class);
        $salesforceProvider->request('GET', '/example/getExample', []);
    }

    public function testFailThenRetryAndSucceedBeforeMaxRetryLimit()
    {
        $restClient = Mockery::mock(RestClientInterface::class);
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
        $salesforceProvider = new SalesforceProviderRestClient(
            $restClient,
            $provider,
            $accessToken,
            $tokenRefreshCallback,
            $maxRetry
        );

        $response = $salesforceProvider->request('GET', '/example/getExample');

        $this->assertSame(200, $response->getStatusCode());
    }
}
