<?php
namespace EventFarm\Restforce\RestClient;

use Psr\Http\Message\StreamInterface;

class RestforceClientException extends \Exception
{
    public static function invalidResponse(StreamInterface $response)
    {
        return new self($response->getContents(), 500);
    }

    public static function invalidJsonResponse(string $message)
    {
        return new self('Invalid JSON Response: ' . $message);
    }
}
