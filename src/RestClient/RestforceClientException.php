<?php
namespace EventFarm\Restforce\RestClient;

use Psr\Http\Message\StreamInterface;

class RestforceClientException extends \Exception
{
    public static function queryError(StreamInterface $responseBody)
    {
        return new self($responseBody->getContents(), 500);
    }
}
