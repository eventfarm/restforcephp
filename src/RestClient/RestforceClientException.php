<?php
namespace EventFarm\Restforce\RestClient;

class RestforceClientException extends \Exception
{
    public function __construct($message, $code = 500, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function invalidResponse(string $message)
    {
        return new self('Invalid Response: ' . $message);
    }

    public static function invalidJsonResponse(string $message)
    {
        return new self('Invalid JSON Response: ' . $message);
    }
}
