<?php
namespace Jmondi\Restforce\Oauth;

interface SalesforceProviderInterface
{
    /**
     * Requests an access token using a specified grant and option set.
     *
     * @param  mixed $grant
     * @param  array $options
     * @return AccessToken
     */
    public function getAccessToken($grant, array $options = []);
}
