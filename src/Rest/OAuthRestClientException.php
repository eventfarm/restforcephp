<?php
namespace EventFarm\Restforce\Rest;

use Exception;

/**
 * Class OAuthRestClientException
 *
 * @package EventFarm\Restforce\Rest
 */
class OAuthRestClientException extends Exception
{
    /**
     * Triggered when access token can't be loaded
     *
     * @return OAuthRestClientException
     */
    public static function unableToLoadAccessToken()
    {
        return new self('Unable to load access token');
    }
}
