<?php
namespace EventFarm\Restforce\Rest;

use Exception;

class OAuthRestClientException extends Exception
{
    public static function unableToLoadAccessToken(?string $message = null)
    {
        $errorMessage = 'Unable to load access token';
        
        if ($message) {
            $errorMessage = $errorMessage . ': ' . $message;
        }

        return new self($errorMessage);
    }
}
