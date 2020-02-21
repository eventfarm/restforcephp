<?php
namespace EventFarm\Restforce;

use \EventFarm\Restforce\Rest\GuzzleRestClient;
use \EventFarm\Restforce\Rest\OAuthAccessToken;
use \EventFarm\Restforce\Rest\OAuthRestClient;
use \EventFarm\Restforce\Rest\SalesforceRestClient;

/**
 * Class Restforce
 *
 * @package EventFarm\Restforce
 */
class Restforce implements RestforceInterface
{
    const USER_INFO_ENDPOINT = 'RESOURCE_OWNER';
    const SALESFORCE_API_ENDPOINT = 'https://voyageprive--preprod.cs109.my.salesforce.com';
    const DEFAULT_API_VERSION = 'v41.0';
    const CSV_EXTENSION = '.csv';
    const FILE_APPEND = 'a';
    const FILE_READONLY = 'r';
    const FILE_WRITE = 'w';
    const BULK_JOB_OPERATION_UPDATE = 'update';
    const BULK_JOB_OPERATION_INSERT = 'insert';

    /** @var string */
    private $clientId;
    /** @var string */
    private $clientSecret;
    /** @var null|string */
    private $username;
    /** @var null|string */
    private $password;
    /** @var OAuthAccessToken|null */
    private $accessToken;
    /** @var string */
    private $apiVersion;
    /** @var OAuthRestClient|null */
    private $oAuthRestClient;
    /** @var string */
    private $apiEndpoint;

    /**
     * Restforce constructor.
     *
     * @param string                $clientId     client id
     * @param string                $clientSecret client secret
     * @param OAuthAccessToken|null $accessToken  access token
     * @param string|null           $username     username
     * @param string|null           $password     password
     * @param string|null           $apiVersion   api version
     * @param string|null           $apiEndpoint  api endpoint
     *
     * @throws RestforceException
     */
    public function __construct(
        string $clientId,
        string $clientSecret,
        OAuthAccessToken $accessToken = null,
        string $username = null,
        string $password = null,
        string $apiVersion = null,
        string $apiEndpoint = null
    ) {
        if ($accessToken === null && $username === null && $password === null) {
            throw RestforceException::minimumRequiredFieldsNotMet();
        }

        if ($apiVersion === null) {
            $apiVersion = self::DEFAULT_API_VERSION;
        }

        if ($apiEndpoint == null) {
            $apiEndpoint = self::SALESFORCE_API_ENDPOINT;
        }

        $this->apiEndpoint = $apiEndpoint;
        $this->apiVersion = $apiVersion;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->accessToken = $accessToken;
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * Create method
     *
     * @param string $sobjectType object type
     * @param array  $data        data
     *
     * @return mixed
     */
    public function create(string $sobjectType, array $data)
    {
        $uri = 'sobjects/' . $sobjectType;

        return $this->getOAuthRestClient()->postJson($uri, $data);
    }

    /**
     * Create bulk job
     *
     * @param string $object           object to work with
     * @param string $operation        operation
     * @param array  $headerColumnList column names for header
     *
     * @return string
     * @throws \Exception
     */
    public function createJob(string $object, string $operation, $headerColumnList)
    {
        if (empty($headerColumnList)) {
            throw new \Exception(
                'header columns are missing on csv file for operation ' . $operation . ' on object ' . $object
            );
        }
        $uri = 'jobs/ingest';

        $job = $this->getOAuthRestClient()->postJson($uri, [
            'operation' => $operation,
            'object' => $object,
            'contentType' => 'CSV'
        ]);

        $jobResponse = json_decode($job->getBody());

        if (!$jobResponse->id) {
            throw new \Exception(
                'An error occurred while creating bulk job of type ' . $operation . ' on object ' . $object
            );
        }

        $fp = fopen($jobResponse->id . self::CSV_EXTENSION, self::FILE_WRITE);
        fputcsv($fp, $headerColumnList);
        fclose($fp);

        return $jobResponse->id;
    }

    /**
     * Add record to bulk job (csv file)
     *
     * @param string $jobId    job id
     * @param array  $dataHash data hash
     *
     * @return void
     * @throws \Exception
     */
    public function addRecordLineToJob(string $jobId, array $dataHash)
    {
        $filePath = $jobId . self::CSV_EXTENSION;

        if (!file_exists($filePath)) {
            throw new \Exception('File ' . $filePath . ' has not been found. Cannot add batch to job ' . $jobId);
        }

        $fp = fopen($filePath, self::FILE_APPEND);
        fputcsv($fp, array_values($dataHash));

        fclose($fp);
    }

    /**
     * Execute bulk job
     *
     * @param string $jobId job id
     *
     * @return mixed
     */
    public function executeJob(string $jobId)
    {
        $uri = 'jobs/ingest/' . $jobId . '/batches';
        return $this->getOAuthRestClient()->putCsv($uri, $jobId . self::CSV_EXTENSION);
    }

    /**
     * Check bulk job status
     *
     * @param string $jobId job id
     *
     * @return mixed
     */
    public function checkJob(string $jobId)
    {
        $uri = 'jobs/ingest/' . $jobId;
        return $this->getOAuthRestClient()->get($uri);
    }

    /**
     * Close bulk job
     *
     * @param string $jobId job id
     *
     * @return void
     */
    public function closeJob(string $jobId)
    {
        $uri = 'jobs/ingest/' . $jobId;

        $this->getOAuthRestClient()->patchJson($uri, [
            'state' => 'UploadComplete'
        ]);

        unlink($jobId . self::CSV_EXTENSION);
    }

    /**
     * Update method
     *
     * @param string $sobjectType object type
     * @param string $sobjectId   object id
     * @param array  $data        data
     *
     * @return mixed
     */
    public function update(string $sobjectType, string $sobjectId, array $data)
    {
        $uri = 'sobjects/' . $sobjectType . '/' . $sobjectId;

        return $this->getOAuthRestClient()->patchJson($uri, $data);
    }

    /**
     * Describe method
     *
     * @param string $sobject object
     *
     * @return mixed
     */
    public function describe(string $sobject)
    {
        $uri = 'sobjects/' . $sobject . '/describe';

        return $this->getOAuthRestClient()->get($uri);
    }

    /**
     * Find method
     *
     * @param string $sobjectType object type
     * @param string $sobjectId   object id
     * @param array  $fields      fields
     *
     * @return mixed
     */
    public function find(string $sobjectType, string $sobjectId, array $fields = [])
    {
        $uri = 'sobjects/' . $sobjectType . '/' . $sobjectId;

        $queryParams = [];

        if (!empty($fields)) {
            $fieldsString = implode(',', $fields);
            $queryParams = ['fields' => $fieldsString];
        }

        return $this->getOAuthRestClient()->get($uri, $queryParams);
    }

    /**
     * Parameterized search method
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
    ) {
        $uri = 'parameterizedSearch';

        return $this->getOAuthRestClient()->postJson($uri, [
            "q" => $search,
            "fields" => $fields,
            "sobjects" => [
                [
                    "name" => $sobjectType,
                    "where" => $whereQuery
                ]
            ]
        ]);
    }

    /**
     * Limits method
     *
     * @return mixed
     */
    public function limits()
    {
        return $this->getOAuthRestClient()->get('/limits');
    }

    /**
     * GetNext method
     *
     * @param string $url url
     *
     * @return mixed
     */
    public function getNext(string $url)
    {
        return $this->getOAuthRestClient()->get($url);
    }

    /**
     * Query method
     *
     * @param string $queryString query string
     *
     * @return mixed
     */
    public function query(string $queryString)
    {
        return $this->getOAuthRestClient()->get('query', [
            'q' => $queryString,
        ]);
    }

    /**
     * UserInfo method
     *
     * @return mixed
     */
    public function userInfo()
    {
        return $this->getOAuthRestClient()->get(self::USER_INFO_ENDPOINT);
    }

    /**
     * Get OAuth rest client
     *
     * @return OAuthRestClient|null
     */
    private function getOAuthRestClient()
    {
        if ($this->oAuthRestClient === null) {
            $this->oAuthRestClient = new OAuthRestClient(
                new SalesforceRestClient(
                    new GuzzleRestClient($this->apiEndpoint),
                    $this->apiVersion
                ),
                new GuzzleRestClient($this->apiEndpoint),
                $this->clientId,
                $this->clientSecret,
                $this->username,
                $this->password,
                $this->accessToken
            );
        }
        return $this->oAuthRestClient;
    }
}
