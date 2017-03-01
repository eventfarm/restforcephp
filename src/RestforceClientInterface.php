<?php
namespace EventFarm\Restforce;

use stdClass;

interface RestforceClientInterface
{
    public function query(string $queryString): stdClass;
    public function describe(string $sobject): stdClass;
    public function create(string $sobject, array $data);
    public function update(string $sobject, string $sobjectId, array $data);
}
