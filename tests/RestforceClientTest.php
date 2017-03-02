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
        $result = $restforceClient->limits();
//        error_log(print_r($result, true));
    }

    public function testUserInfo()
    {
        $restforceClient = $this->getRestforceClient();
        $result = $restforceClient->userInfo();
//        error_log(print_r($result, true));
    }

    public function testQuerySendsCorrectRequest()
    {
        $restforceClient = $this->getRestforceClient();
        $result = $restforceClient->query('SELECT name');
//        error_log(print_r($result, true));
    }


    public function testQueryAllSendsCorrectRequest()
    {
        $restforceClient = $this->getRestforceClient();
        $result = $restforceClient->queryAll('SELECT name');
//        error_log(print_r($result, true));
    }

    public function testExplainSendsCorrectRequest()
    {
        $restforceClient = $this->getRestforceClient();
        $result = $restforceClient->explain('SELECT name');
//        error_log(print_r($result, true));
    }

    public function testFindWithoutParamsSendsCorrectRequest()
    {
        $restforceClient = $this->getRestforceClient();
        $result = $restforceClient->find('Account', '001410000056Kf0AAE');

        $this->assertEquals(FakeRestforceClient::DUMMY_NAME, $result->Name);

    }

    public function testFindWithParamsSendsCorrectRequest()
    {
        $restforceClient = $this->getRestforceClient();
        $result = $restforceClient->find('Account', '001410000056Kf0AAE', [
            'Name' => FakeRestforceClient::DUMMY_NAME,
            'SomethingElse' => 'MySomethingElse'
        ]);

        $this->assertEquals(FakeRestforceClient::DUMMY_NAME, $result->Name);
    }

    public function testDescribeSendsCorrectRequest()
    {
        $restforceClient = $this->getRestforceClient();
        $restforceClient->describe('Account');
        // @TODO Add assertion here
//        error_log($result);
    }

    public function testPicklistValuesSendsCorrectRequest()
    {
        $restforceClient = $this->getRestforceClient();
        $result = $restforceClient->picklistValues('Task', 'Type');
        // @TODO Add assertion here
    }

    public function testCreateSendsCorrectRequest()
    {
        $restforceClient = $this->getRestforceClient();
        $result = $restforceClient->create('Account', [
            'Name' => 'TestCreateNewAccount'
        ]);

        $this->assertEquals(FakeRestforceClient::DUMMY_SOBJECT_ID, $result);
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
        // @TODO Add assertion here
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
        // @TODO Add assertion here
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
