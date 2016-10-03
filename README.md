# Restforce PHP

[![Travis](https://img.shields.io/travis/jasonraimondi/restforcephp.svg?maxAge=2592000?style=flat-square)](https://travis-ci.org/jasonraimondi/restforcephp)
[![Downloads](https://img.shields.io/packagist/dt/jmondi/restforcephp.svg?style=flat-square)](https://packagist.org/packages/jmondi/restforcephp)
[![Packagist](https://img.shields.io/packagist/l/jmondi/restforcephp.svg?maxAge=2592000?style=flat-square)](https://packagist.org/packages/jmondi/restforcephp)
[![Code Climate](https://codeclimate.com/github/jasonraimondi/restforcephp/badges/gpa.svg)](https://codeclimate.com/github/jasonraimondi/restforcephp)
[![Test Coverage](https://codeclimate.com/github/jasonraimondi/restforcephp/badges/coverage.svg)](https://codeclimate.com/github/jasonraimondi/restforcephp/coverage)

This is meant to emulate what the [ejhomes/restforce gem](https://github.com/ejholmes/restforce) is doing.

## Installation

Add the following lines to your ``composer.json`` file.

```json
{
    "require": {
        "jmondi/restforcephp": "dev-master"
    }
}
```

```bash
$ composer install
```

## Unit Tests:

```bash
$ vendor/bin/phpunit
```

### With Code Coverage:

```bash
$ vendor/bin/phpunit --coverage-text --coverage-html coverage_report
```

## Run Coding Standards Test:

```bash
$ vendor/bin/phpcs -p --standard=PSR2 src/ tests/
```

## Example Client Implementation

```php
<?php
namespace App;

use Jmondi\Restforce\RestClient\GuzzleRestClient;
use Jmondi\Restforce\RestforceClient;
use Jmondi\Restforce\SalesforceOauth\SalesforceProviderRestClient;
use Jmondi\Restforce\SalesforceOauth\TokenRefreshCallbackInterface;
use Stevenmaguire\OAuth2\Client\Provider\Salesforce as SalesforceProvider;
use Stevenmaguire\OAuth2\Client\Token\AccessToken;

class DemoSalesforceClient implements TokenRefreshCallbackInterface
{
    const SALESFORCE_CLIENT_ID = 'your salesforce client id';
    const SALESFORCE_CLIENT_SECRET = 'your salesforce client secret';
    const SALESFORCE_CALLBACK = 'callback URL to catch $_GET['code'] to generate AccessToken';
    const INSTANCE_URL = 'salesforce instance url';
    const RESOURCE_OWNER_ID = 'url to salesforce authd user info (from AccessToken)';

    public function tokenRefreshCallback(AccessToken $token)
    {
        // CALLBACK FUNCTION TO STORE THE
        // NEWLY REFRESHED ACCESS TOKEN
        // TO PERSIST IN MEMORY
    }

    public function getProvider():SalesforceProvider
    {
        if (empty($this->salesforce)) {
            $this->salesforce = new SalesforceProvider([
                'clientId' => self::SALESFORCE_CLIENT_ID,
                'clientSecret' => self::SALESFORCE_CLIENT_SECRET,
                'redirectUri' => self::SALESFORCE_CALLBACK,
            ]);
        }
        return $this->salesforce;
    }

    public function getAccessToken():AccessToken
    {
        if (empty($this->accessToken)) {
            if (ACCESS_TOKEN_EXISTS_IN_DB_OR_CACHE())){
                $this->accessToken = // GET ACCESS TOKEN FROM DB/CACHE;
            } else {
                $this->redirectToSalesforceAuth();
            }
        }
        
        return $this->accessToken;
    }

    public function getClient():RestforceClient
    {
        $accessToken = $this->getAccessToken();
        
        if (empty($this->restforce)) {
            $apiVersion = 'v37.0';
            $baseUri = $accessToken->getInstanceUrl() . '/services/data/' . $apiVersion . '/';

            $client =
                new SalesforceProviderRestClient(
                    new GuzzleRestClient(
                        new \GuzzleHttp\Client([
                            'base_uri' => $baseUri,
                            'http_errors' => false
                        ])
                    ),
                    $this->getProvider(),
                    $accessToken,
                    $this
                );

            $this->restforce = new RestforceClient(
                $client,
                self::INSTANCE_URL,
                self::RESOURCE_OWNER_ID,
                $this
            );
        }
        return $this->restforce;
    }
    
    public function redirectToSalesforceAuth()
    {
        $provider = $this->getProvider();
        $authorizationUrl = $provider->getAuthorizationUrl();
        header('Location: ' . $authorizationUrl);
        exit;
    }

    /**
     * Generates the access token from the salesforce callback.
     */
    public function generateAccessTokenFromCode(string $code):AccessToken
    {
        $provider = $this->getProvider();
        try {
            $accessToken = $provider->getAccessToken('authorization_code', [
                'code' => $code
            ]);
        } catch (IdentityProviderException $e) {
            exit($e->getMessage());
        }

        // PERSIST ACCESS TOKEN TO DB/CACHE HERE 

        return $accessToken;
    }
}
```

## Usage

#### Limits
[Docs](https://developer.salesforce.com/docs/atlas.en-us.api_rest.meta/api_rest/resources_limits.htm?search_text=limits) Returns a list of daily API limits for the salesforce api. Refer to the docs for the full list of options.

`public function limits():stdClass`

```php
<?php
$demoSalesforceClient = new DemoSalesforceClient();
$restforce = $demoSalesforceClient->getClient();
$restforce->limits();
// { ... }
```


#### UserInfo

[Docs](https://developer.salesforce.com/docs/atlas.en-us.api_rest.meta/api_rest/resources_limits.htm?search_text=limits) Get info about the logged-in user.

`public function limits():stdClass`

```php
<?php
$demoSalesforceClient = new DemoSalesforceClient();
$restforce = $demoSalesforceClient->getClient();
$restforce->userInfo();
// { ... }
```

#### Query

[Docs](https://developer.salesforce.com/docs/atlas.en-us.api_rest.meta/api_rest/dome_query.htm) Use the Query resource to execute a SOQL query that returns all the results in a single response.

`public function query(string $query):stdClass`

```php
<?php
$demoSalesforceClient = new DemoSalesforceClient();
$restforce = $demoSalesforceClient->getClient();
$restforce->query('SELECT Id, Name FROM Account);
// { ... }
```

#### QueryAll

[Docs](https://developer.salesforce.com/docs/atlas.en-us.api_rest.meta/api_rest/dome_queryall.htm) Include SOQL that includes deleted items.

`public function queryAll(string $query):stdClass`

```php
<?php
$demoSalesforceClient = new DemoSalesforceClient();
$restforce = $demoSalesforceClient->getClient();
$restforce->queryAll('SELECT Id, Name FROM Account);
// { ... }
```

#### Explain
[Docs](https://developer.salesforce.com/docs/atlas.en-us.api_rest.meta/api_rest/dome_query_explain.htm) Get feedback on how Salesforce will execute your query, report, or list view.

`public function explain(string $query):stdClass`

```php
<?php
$demoSalesforceClient = new DemoSalesforceClient();
$restforce = $demoSalesforceClient->getClient();
$restforce->explain('SELECT Id, Name FROM Account);
// { ... }
```

#### Basic

[Docs](https://developer.salesforce.com/docs/atlas.en-us.api_rest.meta/api_rest/resources_sobject_basic_info.htm?search_text=limits) Describes the individual metadata for the specified object.

`public function basic(string $type):stdClass`

```php
<?php
$demoSalesforceClient = new DemoSalesforceClient();
$restforce = $demoSalesforceClient->getClient();
$restforce->find('Account', '001410000056Kf0AAE');
// { ... }
```

#### Find

[Docs](https://developer.salesforce.com/docs/atlas.en-us.api_rest.meta/api_rest/dome_get_field_values.htm?search_text=limits) Find resource `$id` of `$type`, optionally specify the fields you want to retrieve in the fields parameter and use the GET method of the resource.

`public function find(string $type, string $id, array $fields = []):stdClass`

```php
<?php
$demoSalesforceClient = new DemoSalesforceClient();
$restforce = $demoSalesforceClient->getClient();
$restforce->find('Account', '001410000056Kf0AAE');
// { ... }
```

#### Describe

[Docs](https://developer.salesforce.com/docs/atlas.en-us.api_rest.meta/api_rest/resources_sobject_describe.htm?search_text=describe) Completely describes the individual metadata at all levels for the specified object.

`public function describe(string $type):stdClass`

```php
<?php
$demoSalesforceClient = new DemoSalesforceClient();
$restforce = $demoSalesforceClient->getClient();
$restforce->describe('Account');
// { ... }
```


#### Create

[Docs](https://developer.salesforce.com/docs/atlas.en-us.api_rest.meta/api_rest/dome_sobject_create.htm) Create new records of `$type`. The response body will contain the ID of the created record if the call is successful.

`public function create(string $type, array $data):stdClass`

```php
<?php
$demoSalesforceClient = new DemoSalesforceClient();
$restforce = $demoSalesforceClient->getClient();
$restforce->create('Account', [
    'Name' => 'Foo Bar'
]);
// '001i000001ysdBGAAY'` 
```

#### Update

[Docs](https://developer.salesforce.com/docs/atlas.en-us.api_rest.meta/api_rest/dome_update_fields.htm?search_text=describe) You use the SObject Rows resource to update records. The response will be the a bool of `$success`.

`public function update(string $type, string $id, array $data):bool`

```php
<?php
$demoSalesforceClient = new DemoSalesforceClient();
$restforce = $demoSalesforceClient->getClient();
$restforce->update('Account', '001i000001ysdBGAAY' [
    'Name' => 'Foo Bar Two'
]);
// true|false
```

#### Destroy

[Docs](https://developer.salesforce.com/docs/atlas.en-us.api_rest.meta/api_rest/dome_delete_record.htm?search_text=describe) Delete record of `$type` and `$id`. The response will be the a bool of `$success`.

`public function destroy(string $type, string $id):bool`

```php
<?php
$demoSalesforceClient = new DemoSalesforceClient();
$restforce = $demoSalesforceClient->getClient();
$restforce->destroy('Account', '001i000001ysdBGAAY');
// true|false
```
