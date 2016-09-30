<?php
namespace Jmondi\Restforce;

use Jmondi\Restforce\Token\TokenRefreshCallbackInterface;
use Psr\Http\Message\ResponseInterface;
use Stevenmaguire\OAuth2\Client\Provider\Salesforce as SalesforceProvider;
use Stevenmaguire\OAuth2\Client\Token\AccessToken;
use Mockery as Mock;

class SalesforceProviderRestClientTest extends \PHPUnit_Framework_TestCase
{
    public function testRetryOverLimit()
    {
        $restClient = Mock::mock(RestClientInterface::class);
        $provider = Mock::mock(SalesforceProvider::class);
        $accessToken = Mock::mock(AccessToken::class);
        $tokenRefreshCallback = Mock::mock(TokenRefreshCallbackInterface::class);


        $accessToken->shouldReceive('getToken')
            ->andReturn('TOKENSDKLJLKJWEF');


        $failedResponse = Mock::mock(ResponseInterface::class);
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

        $response = $salesforceProvider->request('GET', '/example/getExample', []);

        $this->assertSame(401, $response->getStatusCode());

    }
    public function testFailTwiceThenSucceed()
    {
        $restClient = Mock::mock(RestClientInterface::class);
        $provider = Mock::mock(SalesforceProvider::class);
        $accessToken = Mock::mock(AccessToken::class);
        $tokenRefreshCallback = Mock::mock(TokenRefreshCallbackInterface::class);

        $accessToken->shouldReceive('getToken')
            ->andReturn('TOKENSDKLJLKJWEF');

        $failedResponse = Mock::mock(ResponseInterface::class);
        $failedResponse->shouldReceive('getStatusCode')
            ->andReturn(401);

        $successResponse = Mock::mock(ResponseInterface::class);
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

        $response = $salesforceProvider->request('GET', '/example/getExample', []);

        $this->assertSame(200, $response->getStatusCode());
    }
}
