# Restforce PHP

This is meant to emulate what the [ejhomes/restforce gem](https://github.com/ejholmes/restforce) is doing.

## Example Client Implementation

```php
<?php
namespace App;

use Stevenmaguire\OAuth2\Client\Token\AccessToken;
use Jmondi\Restforce\Token\TokenRefreshCallbackInterface;

class DemoClient implements TokenRefreshCallbackInterface
{

    const SALESFORCE_CLIENT_ID = 'your salesforce client id';
    const SALESFORCE_CLIENT_SECRET = 'your salesforce client secret';
    const SALESFORCE_CALLBACK = 'callback URL to catch $_GET['code'] to generate AccessToken';
    const ACCESS_TOKEN = 'access token string, different from AccessToken object';
    const REFRESH_TOKEN = 'refresh token sring';
    const INSTANCE_URL = 'salesforce instance url';
    const RESOURCE_OWNER_ID = 'url to salesforce auth'd user info (from AccessToken)';

    public function tokenRefreshCallback(AccessToken $token):void
    {
        // CALLBACK FUNCTION TO STORE THE
        // NEWLY REFRESHED ACCESS TOKEN
    }
    
    public function getClient()
    {
        $accessToken = $this-getAccessToken();
        
        $client =
            new SalesforceProviderRestClient(
                new GuzzleRestClient(
                    new \GuzzleHttp\Client([
                        'base_uri' => $baseUri,
                        'http_errors' => false
                    ])
                ),
                new SalesforceProvider([
                    'clientId' => self::SALESFORCE_CLIENT_ID,
                    'clientSecret' => self::SALESFORCE_CLIENT_SECRET,
                    'redirectUri' => self::SALESFORCE_CALLBACK,
                ]),
                $accessToken,
                $this
            );
        
        $restforce = new RestforceClient(
            $client,
            self::ACCESS_TOKEN,
            self::REFRESH_TOKEN,
            self::INSTANCE_URL,
            self::SALESFORCE_CLIENT_ID,
            self::SALESFORCE_CLIENT_SECRET,
            self::SALESFORCE_CALLBACK,
            self::RESOURCE_OWNER_ID,
            $this
        );
    }
    
    private function getAccessToken():AccessToken
    {
        $accessToken = getAccessTokenFromCache()
        return $accessToken;
    }
}
```

## Usage


#### Query
```
$salesforce = new DemoClient();
$restforce = $salesforceClient->getClient();
$restforce->query('SELECT Id, Name FROM Account);
```


#### UserInfo
```
$salesforce = new DemoClient();
$restforce = $salesforceClient->getClient();
$restforce->userInfo();
```

#### Find
```
$salesforce = new DemoClient();
$restforce = $salesforceClient->getClient();
$restforce->find('Account', '001410000056Kf0AAE');
```


#### Limits
```
$salesforce = new DemoClient();
$restforce = $salesforceClient->getClient();
$restforce->limits();
```

#### Create
```
$salesforce = new DemoClient();
$restforce = $salesforceClient->getClient();
$restforce->create('Account', [
    'Name' => 'Foo Bar'
]);
```
