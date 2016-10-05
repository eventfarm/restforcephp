<?php
namespace Jmondi\Restforce;

use Jmondi\Restforce\Oauth\AccessToken;

interface TokenRefreshInterface
{
    public function tokenRefreshCallback(AccessToken $accessToken);
}
