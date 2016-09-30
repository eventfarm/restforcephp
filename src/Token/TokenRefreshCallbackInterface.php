<?php
namespace EventFarm\Restforce\Token;

use Stevenmaguire\OAuth2\Client\Token\AccessToken;

interface TokenRefreshCallbackInterface
{
    public function tokenRefreshCallback(AccessToken $accessToken):bool;
}
