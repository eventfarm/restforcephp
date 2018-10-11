<?php
namespace EventFarm\Restforce;

use Psr\Http\Message\ResponseInterface;

interface RestforceInterface
{
    public function userInfo();
    public function limits();
    public function query(string $soqlQuery);
    public function create(string $sobjectType, array $data);
    public function update(string $sobjectType, string $sobjectId, array $data);
    public function describe(string $sobjectType);
    public function find(string $sobjectType, string $sobjectId, array $fields = []);
    public function getNext(string $url);
    public function parameterizedSearch(
        string $sobjectType,
        string $search,
        array $fields = [],
        string $whereQuery = null
    );
}
