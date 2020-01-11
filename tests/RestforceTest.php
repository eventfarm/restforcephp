<?php
namespace EventFarm\Restforce\Test;

use EventFarm\Restforce\Restforce;
use EventFarm\Restforce\RestforceException;

class RestforceTest extends AbstractRestforceTestCase
{
    private const DUMMY_CLIENT_ID = 'dummyClientId';
    private const DUMMY_CLIENT_SECRET = 'dummyClientSecret';
    private const NULL_ACCESS_TOKEN = null;
    private const NULL_USERNAME = null;
    private const NULL_PASSWORD = null;

    public function testSomethingCrazy()
    {
        $restforce = new Restforce(
            getenv('SALESFORCE_OAUTH_CLIENT'),
            getenv('SALESFORCE_OAUTH_SECRET'),
            self::NULL_ACCESS_TOKEN,
            getenv('SALESFORCE_USERNAME'),
            getenv('SALESFORCE_PASSWORD'),
            null,
            "https://test.salesforce.com/"
        );
        $this->assertSame(200, $restforce->limits()->getStatusCode());
        $this->assertSame(200, $restforce->getNext(Restforce::USER_INFO_ENDPOINT)->getStatusCode());
        $this->assertSame(200, $restforce->userInfo()->getStatusCode());
        $this->assertSame(200, $restforce->describe('Lead')->getStatusCode());
        $this->assertSame(200, $restforce->query('SELECT Id FROM Campaign')->getStatusCode());
        $this->assertNotNull($restforce->create('Contact', [
            'FirstName' => 'Jason',
            'LastName' => 'Raimondi',
            'Email' => 'jason@raimondi.us',
        ]));
    }

    public function testExceptionIsThrownIfRestforceDoesntHaveEnoughToStart()
    {
        $this->expectException(RestforceException::class);
        new Restforce(
            self::DUMMY_CLIENT_ID,
            self::DUMMY_CLIENT_SECRET,
            self::NULL_ACCESS_TOKEN,
            self::NULL_USERNAME,
            self::NULL_PASSWORD
        );
    }
}
