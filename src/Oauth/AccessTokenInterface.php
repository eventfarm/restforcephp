<?php
namespace Jmondi\Restforce\Oauth;

interface AccessTokenInterface
{
    public function getToken():string;

    public function getRefreshToken():string;

    public function getInstanceUrl():string;
}
