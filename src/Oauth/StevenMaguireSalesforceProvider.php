<?php
namespace EventFarm\Restforce\Oauth;

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

    public function getAuthorizationUrl(array $options = []): string
    {
        return $this->salesforce->getAuthorizationUrl($options);
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

        if ($grant === 'refresh_token' && isset($options['refresh_token'])) {
            $refreshToken = $options['refresh_token'];
        } else {
            $refreshToken = $salesforceAccessToken->getRefreshToken();
        }

        $accessToken = $salesforceAccessToken->getToken();
        $instanceUrl = $salesforceAccessToken->getResourceOwnerId();
        $values = $salesforceAccessToken->getValues();

        return new AccessToken(
            $accessToken,
            $refreshToken,
            $instanceUrl,
            $values
        );
    }
}
