<?php
namespace Jmondi\Restforce;

use Jmondi\Restforce\Oauth\AccessTokenInterface;
use Jmondi\Restforce\Oauth\SalesforceProvider;
use Jmondi\Restforce\Oauth\SalesforceProviderInterface;
use Mockery;

class SalesforceProviderTest extends \PHPUnit_Framework_TestCase
{

    public function testLeagueAccessTokenFacade()
    {
        $salesforceProviderInterface = Mockery::mock(SalesforceProviderInterface::class);
        $restforceAccessToken = Mockery::instanceMock(AccessTokenInterface::class);

        $restforceSalesforceProvider = new SalesforceProvider(
            $salesforceProviderInterface
        );

        $salesforceProviderInterface->shouldReceive('getAccessToken')
            ->with('', [])
            ->andReturn($restforceAccessToken);

        $restforceSalesforceProvider->getAccessToken('', []);
    }
}
