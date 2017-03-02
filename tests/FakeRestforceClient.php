<?php
namespace EventFarm\Restforce\Tests;

use EventFarm\Restforce\RestforceClient;
use stdClass;

class FakeRestforceClient extends RestforceClient
{
    const DUMMY_SOBJECT_ID = 'DUMMYSOBJECTID';
    const DUMMY_NAME = 'My First Whizbang';
    /** @var int */
    private $responseStatusCode;

    public function __construct(int $responseStatusCode)
    {
        $this->responseStatusCode = $responseStatusCode;
    }

    public function query(string $queryString): stdClass
    {
        return json_decode(file_get_contents(__DIR__ . '/SampleResponse/sobject/query_success_response.json'));
    }

    public function describe(string $sobject): stdClass
    {
        return json_decode(file_get_contents(__DIR__ . '/SampleResponse/sobject/describe_sobjects_success_response.json'));
    }

    public function create(string $sobject, array $data)
    {
        if ($this->isValidResponse()) {
            return self::DUMMY_SOBJECT_ID;
        } else {
            return false;
        }
    }

    public function update(string $sobject, string $sobjectId, array $data)
    {
        if ($this->isValidResponse()) {
            return true;
        } else {
            return false;
        }
    }

    public function userInfo(): stdClass
    {
        return json_decode(file_get_contents(__DIR__ . '/SampleResponse/user_info_success_response.json'));

    }

    public function queryAll(string $queryString): stdClass
    {
        return json_decode(file_get_contents(__DIR__ . '/SampleResponse/sobject/query_success_response.json'));

    }

    public function explain(string $explainString): stdClass
    {
        // TODO: Implement explain() method.
        return json_decode(file_get_contents(__DIR__ . '/SampleResponse/sobject/query_success_response.json'));

    }

    public function find(string $sobject, string $sobjectId = null, array $fields = []): stdClass
    {
        return json_decode(file_get_contents(__DIR__ . '/SampleResponse/sobject/sobject_find_success_response.json'));

    }

    public function picklistValues(string $sobject, string $field)
    {
    }

    public function fieldList(string $sobject)
    {
    }

    public function limits(): stdClass
    {
        return json_decode(file_get_contents(__DIR__ . '/SampleResponse/limits_success_response.json'));

    }

    public function destroy(string $sobject, string $sobjectId): bool
    {
        if ($this->isValidResponse()) {
            return file_get_contents(__DIR__ . '/SampleResponse/sobject/get_deleted_success_response.json');
        } else {
            return false;
        }
    }

    /**
     * @return bool
     */
    private function isValidResponse(): bool
    {
        return $this->responseStatusCode >= 200 && $this->responseStatusCode <= 399;
    }
}
