<?php
namespace Jmondi\Restforce\Oauth;

use Stevenmaguire\OAuth2\Client\Provider\Salesforce as StevenMaguireSalesforceProvider;

class SalesforceProvider implements SalesforceProviderInterface
{
    public function __construct(StevenMaguireSalesforceProvider $salesforceProvider)
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
        $this->salesforceProvider->getAccessToken($grant, $options);
    }
}
