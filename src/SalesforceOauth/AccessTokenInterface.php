<?php
namespace Jmondi\Restforce\SalesforceOauth;

use Stevenmaguire\OAuth2\Client\Token\AccessToken;

interface AccessTokenInterface
{
    public function getAccessToken():AccessToken;
}
