<?php
namespace Jmondi\Restforce\Oauth;

class SalesforceProvider implements SalesforceProviderInterface
{
    private $salesforceProvider;

    /**
     * @param SalesforceProviderInterface $salesforceProvider
     */
    public function __construct($salesforceProvider)
    {
        $this->salesforceProvider = $salesforceProvider;
    }

    /**
     * Requests an access token using a specified grant and option set.
     *
     * @param  mixed $grant
     * @param  array $options
     * @return AccessTokenInterface
     */
    public function getAccessToken($grant, array $options = [])
    {
        $accessToken = $this->salesforceProvider->getAccessToken($grant, $options);
        return new AccessToken($accessToken);
    }

    public function getAuthorizationUrl():string
    {
        return $this->salesforceProvider->getAuthorizationUrl();
    }
}
