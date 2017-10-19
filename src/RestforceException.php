<?php
namespace EventFarm\Restforce;

use Throwable;

class RestforceException extends \Exception
{
    public function __construct(string $message, int $code = 500, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function minimumRequiredFieldsNotMet()
    {
        return new self('Restforce needs either an OAuthToken or User/PW combo to start authenticate');
    }
}
