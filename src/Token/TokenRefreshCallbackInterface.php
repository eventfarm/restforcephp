<?php
namespace Jmondi\Restforce\Token;

use Stevenmaguire\OAuth2\Client\Token\AccessToken;

interface TokenRefreshCallbackInterface
{
    public function tokenRefreshCallback(AccessToken $accessToken);
}
