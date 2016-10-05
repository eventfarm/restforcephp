<?php
namespace Jmondi\Restforce\Oauth;

interface AccessTokenInterface
{
    public function getRefreshToken():string;
    public function getResourceOwnerId():string;
    public function getInstanceUrl():string;
    public function getToken():string;
}
