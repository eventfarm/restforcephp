<?php
namespace Jmondi\Restforce\Test\Oauth;

use Jmondi\Restforce\Oauth\StevenMaguireSalesforceProvider;
use League\OAuth2\Client\Token\AccessToken;
use Mockery;
use Stevenmaguire\OAuth2\Client\Provider\Salesforce;

class StevenMaguireSalesforceProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testLeagueAccessTokenFacade()
    {
        $myAccessToken = 'myAccessToken';
        $myRefreshToken = 'myRefreshToken';
        $myResourceOwnerId= 'myResourceOwnerId';
        $theLeagueAccessToken = Mockery::mock(AccessToken::class);
        $theLeagueAccessToken->shouldReceive('getToken')->andReturn($myAccessToken);
        $theLeagueAccessToken->shouldReceive('getRefreshToken')->andReturn($myRefreshToken);
        $theLeagueAccessToken->shouldReceive('getResourceOwnerId')->andReturn($myResourceOwnerId);

        $salesforceProvider = Mockery::mock(Salesforce::class);
        $salesforceProvider->shouldReceive('getAccessToken')
            ->with('refresh_token', [
                'refresh_token' => $myRefreshToken
            ])
            ->andReturn($theLeagueAccessToken);

        $stevenMaguireSalesforceProvider = new StevenMaguireSalesforceProvider($salesforceProvider);

        $accessToken = $stevenMaguireSalesforceProvider->getAccessToken('refresh_token', [
            'refresh_token' => $myRefreshToken
        ]);

        $this->assertInstanceOf(\Jmondi\Restforce\Oauth\AccessToken::class, $accessToken);
        $this->assertSame($myAccessToken, $accessToken->getToken());
        $this->assertSame($myRefreshToken, $accessToken->getRefreshToken());
        $this->assertSame($myResourceOwnerId, $accessToken->getInstanceUrl());
    }
}
