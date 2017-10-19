<?php
namespace EventFarm\RestforceV2\Rest;

use Exception;

class OAuthRestClientException extends Exception
{
    public static function unableToLoadAccessToken()
    {
        return new self('Unable to load access token');
    }
}
