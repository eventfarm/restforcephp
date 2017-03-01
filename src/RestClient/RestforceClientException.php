<?php
namespace EventFarm\Restforce\RestClient;

use Psr\Http\Message\StreamInterface;

class RestforceClientException extends \Exception
{
    public static function queryError(StreamInterface $responseBody)
    {
        return new self('Error communicating with Salesforce: ' . $responseBody->getContents(), 500);
    }
}
