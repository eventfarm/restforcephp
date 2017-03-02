<?php
namespace EventFarm\Restforce\Oauth;

interface SalesforceProviderInterface
{
    public function getAuthorizationUrl(array $options = []): string;

    /**
     * Requests an access token using a specified grant and option set.
     *
     * @param  mixed $grant
     * @param  array $options
     * @return AccessTokenInterface
     */
    public function getAccessToken($grant, array $options = []):AccessTokenInterface;
}
