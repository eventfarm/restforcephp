<?php
namespace Jmondi\Restforce;

use Jmondi\Restforce\Oauth\AccessToken;

interface TokenRefreshCallbackInterface
{
    public function tokenRefreshCallback(AccessToken $accessToken);
}
