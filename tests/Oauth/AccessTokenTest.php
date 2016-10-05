<?php
namespace Jmondi\Restforce;

use Jmondi\Restforce\Oauth\AccessToken;
use Jmondi\Restforce\Oauth\AccessTokenInterface;
use Mockery;

class AccessTokenTest extends \PHPUnit_Framework_TestCase
{

    public function testLeagueAccessTokenFacade()
    {
        $stevenMaguireAccessToken = Mockery::mock(
            AccessTokenInterface::class
        );

        $restforceAccessToken = new AccessToken(
            $stevenMaguireAccessToken
        );

        $stevenMaguireAccessToken->shouldReceive('getInstanceUrl')
            ->andReturn('http://instanceUrl.com');

        $stevenMaguireAccessToken->shouldReceive('getRefreshToken')
            ->andReturn('REFRESH_TOKEN');

        $stevenMaguireAccessToken->shouldReceive('getResourceOwnerId')
            ->andReturn('RESOURCE_OWNER_ID');

        $stevenMaguireAccessToken->shouldReceive('getToken')
            ->andReturn('TOKEN');


        $this->assertSame('http://instanceUrl.com', $restforceAccessToken->getInstanceUrl());
        $this->assertSame('REFRESH_TOKEN', $restforceAccessToken->getRefreshToken());
        $this->assertSame('RESOURCE_OWNER_ID', $restforceAccessToken->getResourceOwnerId());
        $this->assertSame('TOKEN', $restforceAccessToken->getToken());
    }
}
