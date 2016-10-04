<?php
namespace Jmondi\Restforce;

use Jmondi\Restforce\Oauth\AccessToken;
use Jmondi\Restforce\Oauth\AccessTokenInterface;
use Jmondi\Restforce\Oauth\SalesforceProvider;
use Mockery;
use Stevenmaguire\OAuth2\Client\Provider\Salesforce as StevenMaguireSalesforceProvider;

class SalesforceProviderTest extends \PHPUnit_Framework_TestCase
{

    public function testLeagueAccessTokenFacade()
    {
        $stevenMaguireSalesforceProvider = Mockery::mock(
            StevenMaguireSalesforceProvider::class
        );

        $restforceAccessToken = Mockery::instanceMock(AccessTokenInterface::class);

        $restforceSalesforceProvider = new SalesforceProvider(
            $stevenMaguireSalesforceProvider
        );

        $stevenMaguireSalesforceProvider->shouldReceive('getAccessToken')
            ->with('', [])
            ->andReturn($restforceAccessToken);

        $restforceSalesforceProvider->getAccessToken('', []);
    }
}
