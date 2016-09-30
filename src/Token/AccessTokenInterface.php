<?php
namespace Jmondi\Restforce\Token;

use Stevenmaguire\OAuth2\Client\Token\AccessToken;

interface AccessTokenInterface
{
    public function getAccessToken():AccessToken;
}
