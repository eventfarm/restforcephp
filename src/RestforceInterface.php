<?php
namespace EventFarm\Restforce;

use Psr\Http\Message\ResponseInterface;

/**
 * Interface RestforceInterface
 *
 * @package EventFarm\Restforce
 */
interface RestforceInterface
{
    /**
     * UserInfo method
     *
     * @return mixed
     */
    public function userInfo();

    /**
     * Limits method
     *
     * @return mixed
     */
    public function limits();

    /**
     * Query method
     *
     * @param string $soqlQuery query
     *
     * @return mixed
     */
    public function query(string $soqlQuery);

    /**
     * Create method
     *
     * @param string $sobjectType object type
     * @param array  $data        data
     *
     * @return mixed
     */
    public function create(string $sobjectType, array $data);

    /**
     * Update method
     *
     * @param string $sobjectType object type
     * @param string $sobjectId   object id
     * @param array  $data        data
     *
     * @return mixed
     */
    public function update(string $sobjectType, string $sobjectId, array $data);

    /**
     * Describe method
     *
     * @param string $sobjectType object type
     *
     * @return mixed
     */
    public function describe(string $sobjectType);

    /**
     * Find method
     *
     * @param string $sobjectType object type
     * @param string $sobjectId   object id
     * @param array  $fields      fields
     *
     * @return mixed
     */
    public function find(string $sobjectType, string $sobjectId, array $fields = []);

    /**
     * GetNext method
     *
     * @param string $url url
     *
     * @return mixed
     */
    public function getNext(string $url);

    /**
     * ParameterizedSearch method
     *
     * @param string      $sobjectType object type
     * @param string      $search      search query
     * @param array       $fields      fields
     * @param string|null $whereQuery  where query
     *
     * @return mixed
     */
    public function parameterizedSearch(
        string $sobjectType,
        string $search,
        array $fields = [],
        string $whereQuery = null
    );
}
