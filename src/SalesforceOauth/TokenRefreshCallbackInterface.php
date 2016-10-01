<?php
namespace Jmondi\Restforce\SalesforceOauth;

use Stevenmaguire\OAuth2\Client\Token\AccessToken;

interface TokenRefreshCallbackInterface
{
    public function tokenRefreshCallback(AccessToken $accessToken);
}
