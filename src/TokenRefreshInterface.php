<?php
namespace EventFarm\Restforce;

use EventFarm\Restforce\Oauth\AccessToken;

interface TokenRefreshInterface
{
    public function tokenRefreshCallback(AccessToken $accessToken);
}
