<?php
namespace EventFarm\Restforce\Tests;

use EventFarm\Restforce\RestforceClientInterface;

class RestforceClientTest extends \PHPUnit_Framework_TestCase
{
    const API_VERSION = 'v37.0';
    const INSTANCE_URL = 'myInstanceUrl';
    const RESOURCE_OWNER_URL = 'http://myResourceOwnerUrl';

    public function testLimitsSendsCorrectRequest()
    {
        $restforceClient = $this->getRestforceClient();
        $restforceClient->limits();
    }

    public function testUserInfo()
    {
        $restforceClient = $this->getRestforceClient();
        $restforceClient->userInfo();
    }

    public function testQuerySendsCorrectRequest()
    {
        $restforceClient = $this->getRestforceClient();
        $restforceClient->query('SELECT name');
    }


    public function testQueryAllSendsCorrectRequest()
    {
        $restforceClient = $this->getRestforceClient();
        $restforceClient->queryAll('SELECT name');
    }

    public function testExplainSendsCorrectRequest()
    {
        $restforceClient = $this->getRestforceClient();
        $restforceClient->explain('SELECT name');
    }

    public function testFindWithoutParamsSendsCorrectRequest()
    {
        $restforceClient = $this->getRestforceClient();
        $restforceClient->find('Account', '001410000056Kf0AAE');
    }

    public function testFindWithParamsSendsCorrectRequest()
    {
        $restforceClient = $this->getRestforceClient();
        $restforceClient->find('Account', '001410000056Kf0AAE', [
            'Name' => 'MyName',
            'SomethingElse' => 'MySomethingElse'
        ]);
    }

    public function testDescribeSendsCorrectRequest()
    {
        $restforceClient = $this->getRestforceClient();
        $restforceClient->describe('Account');
    }

    public function testPicklistValuesSendsCorrectRequest()
    {
        $restforceClient = $this->getRestforceClient();
        $restforceClient->picklistValues('Task', 'Type');
    }

    public function testCreateSendsCorrectRequest()
    {
        $restforceClient = $this->getRestforceClient();
        $restforceClient->create('Account', [
            'Name' => 'TestCreateNewAccount'
        ]);
    }

    public function testCreateSuccessReturnsIdOfNewObject()
    {
        $restforceClient = $this->getRestforceClient(201);
        $result = $restforceClient->create('Account', [
            'Name' => 'TestCreateNewAccount'
        ]);

        $this->assertEquals(FakeRestforceClient::DUMMY_SOBJECT_ID, $result);
    }

    public function testCreateFailureReturnsFalse()
    {
        $restforceClient = $this->getRestforceClient(400);
        $result = $restforceClient->create('Account', [
            'Name' => 'TestCreateNewAccount'
        ]);

        $this->assertFalse($result);
    }

    public function testUpdateSendsCorrectRequest()
    {
        $restforceClient = $this->getRestforceClient(204);
        $restforceClient->update('Account', '001410000056Kf0AAE', [
            'Name' => 'My Updated Name'
        ]);
    }

    public function testUpdateSuccessReturnsTrue()
    {
        $restforceClient = $this->getRestforceClient(204);
        $result = $restforceClient->update('Account', '001410000056Kf0AAE', [
            'Name' => 'My Updated Name'
        ]);

        $this->assertTrue($result);
    }

    public function testUpdateFailureReturnsFalse()
    {
        $restforceClient = $this->getRestforceClient(400);
        $result = $restforceClient->update('Account', '001410000056Kf0AAE', [
            'Name' => 'My Updated Name'
        ]);

        $this->assertFalse($result);
    }

    public function testDestroySendsCorrectRequest()
    {
        $restforceClient = $this->getRestforceClient(204);
        $restforceClient->destroy('Account', '001410000056Kf0AAE');
    }

    public function testDestroySuccessReturnsTrue()
    {
        $restforceClient = $this->getRestforceClient(204);
        $result = $restforceClient->destroy('Account', '001410000056Kf0AAE');

        $this->assertTrue($result);
    }

    public function testDestroyFailureReturnsFalse()
    {
        $restforceClient = $this->getRestforceClient(400);
        $result = $restforceClient->destroy('Account', '001410000056Kf0AAE');

        $this->assertFalse($result);
    }

    private function getRestforceClient(int $responseStatusCode = 200): RestforceClientInterface {
        return new FakeRestforceClient($responseStatusCode);
    }
}
