<?php
namespace EventFarm\Restforce\Rest;

use Exception;

class OAuthRestClientException extends Exception
{
    public static function unableToLoadAccessToken()
    {
        return new self('Unable to load access token');
    }
}
