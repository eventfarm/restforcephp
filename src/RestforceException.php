<?php
namespace EventFarm\Restforce;

use Throwable;

/**
 * Class RestforceException
 *
 * @package EventFarm\Restforce
 */
class RestforceException extends \Exception
{
    /**
     * RestforceException constructor.
     *
     * @param string         $message  message
     * @param int            $code     error code
     * @param Throwable|null $previous previous
     */
    public function __construct(string $message, int $code = 500, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Triggered when missing required fields
     *
     * @return RestforceException
     */
    public static function minimumRequiredFieldsNotMet()
    {
        return new self('Restforce needs either an OAuthToken or User/PW combo to start authenticate');
    }
}
