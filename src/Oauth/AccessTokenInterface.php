<?php
namespace EventFarm\Restforce\Oauth;

interface AccessTokenInterface
{
    public function getToken():string;

    public function getRefreshToken():string;

    public function getInstanceUrl():string;

    public function getValues();
}
