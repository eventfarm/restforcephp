<?php
namespace Jmondi\Restforce\Oauth;

use Stevenmaguire\OAuth2\Client\Provider\Salesforce;

class StevenMaguireSalesforceProvider implements SalesforceProviderInterface
{
    /**
     * @var Salesforce
     */
    private $salesforce;

    public static function createDefaultProvider(
        string $clientId,
        string $clientSecret,
        string $redirectUrl,
        string $domain
    ) {
        return new self(
            new Salesforce([
                'clientId' => $clientId,
                'clientSecret' => $clientSecret,
                'redirectUri' => $redirectUrl,
                'domain' => $domain
            ])
        );
    }

    public function __construct(Salesforce $salesforce)
    {
        $this->salesforce = $salesforce;
    }

    /**
     * Requests an access token using a specified grant and option set.
     *
     * @param  mixed $grant
     * @param  array $options
     * @return AccessTokenInterface
     */
    public function getAccessToken($grant, array $options = []):AccessTokenInterface
    {
        $salesforceAccessToken = $this->salesforce->getAccessToken($grant, $options);
        return new AccessToken(
            $salesforceAccessToken->getToken(),
            $salesforceAccessToken->getRefreshToken(),
            $salesforceAccessToken->getResourceOwnerId()
        );
    }
}
