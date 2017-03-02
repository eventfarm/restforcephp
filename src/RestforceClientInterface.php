<?php
namespace EventFarm\Restforce;

use stdClass;

interface RestforceClientInterface
{
    public function userInfo(): stdClass;

    public function query(string $queryString): stdClass;

    public function queryAll(string $queryString): stdClass;

    public function explain(string $explainString): stdClass;

    public function find(string $sobject, string $sobjectId = null, array $fields = []): stdClass;

    public function describe(string $sobject): stdClass;

    public function picklistValues(string $sobject, string $field);

    public function fieldList(string $sobject);

    public function limits(): stdClass;

    /**
     * @param string $sobject
     * @param array $data
     * @return string | bool
     */
    public function create(string $sobject, array $data);

    public function update(string $sobject, string $sobjectId, array $data);

    public function destroy(string $sobject, string $sobjectId): bool;
}
