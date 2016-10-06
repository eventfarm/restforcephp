# Restforce PHP

[![Travis](https://img.shields.io/travis/jasonraimondi/restforcephp.svg?maxAge=2592000?style=flat-square)](https://travis-ci.org/jasonraimondi/restforcephp)
[![Downloads](https://img.shields.io/packagist/dt/jmondi/restforcephp.svg?style=flat-square)](https://packagist.org/packages/jmondi/restforcephp)
[![Packagist](https://img.shields.io/packagist/l/jmondi/restforcephp.svg?maxAge=2592000?style=flat-square)](https://packagist.org/packages/jmondi/restforcephp)
[![Code Climate](https://codeclimate.com/github/jasonraimondi/restforcephp/badges/gpa.svg)](https://codeclimate.com/github/jasonraimondi/restforcephp)
[![Test Coverage](https://codeclimate.com/github/jasonraimondi/restforcephp/badges/coverage.svg)](https://codeclimate.com/github/jasonraimondi/restforcephp/coverage)

This is meant to emulate what the [ejhomes/restforce gem](https://github.com/ejholmes/restforce) is doing.

## Installation

Our rest client implements the PSR-7 HTTP message interface. Our example implementation is using the [GuzzleHttp](https://github.com/guzzle/guzzle) library, but you are free to use any that returns a [ResponseInterface](https://github.com/php-fig/http-message/blob/master/src/ResponseInterface.php).


```
$ composer require jmondi/restforcephp
```

Or.

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

## Example Client Implementation

Our example client implementation is using [GuzzleHttp](https://github.com/guzzle/guzzle) for sending Http requests, [The PHP League's Oauth Client](https://github.com/thephpleague/oauth2-client) and [Steven Maguires Salesforce Provider](https://github.com/stevenmaguire/oauth2-salesforce) for Salesforce Authentication.

### GuzzleRestClient

Our rest client implements the PSR-7 HTTP message interface. Our example implementation is using the [GuzzleHttp](https://github.com/guzzle/guzzle) library, but you are free to use any that returns a [ResponseInterface](https://github.com/php-fig/http-message/blob/master/src/ResponseInterface.php).

You can either use the provided [GuzzleRestClient](./src/RestClient/GuzzleRestClient.php) or have your own that implements our [RestClientInterface](./src/RestClient/RestClientInterface.php).

### DemoSalesforceProvider

Our Demo Salesforce Provider is using [Steven Maguires Salesforce Provider](https://github.com/stevenmaguire/oauth2-salesforce) library. We chose this library as it is on the list of The PHP Leagues Oauth Client. 

You can either use the provided [SalesforceProvider](./src/Oauth/SalesforceProvider.php) or have your own that implements our [SalesforceProviderInterface](./src/Oauth/SalesforceProviderInterface.php).

### DemoAccessToken

Our DemoAccessToken is using [Steven Maguire's AccessToken](https://github.com/stevenmaguire/oauth2-salesforce/tree/master/src/Token) which is an extension of PHP League's Access Token.

You can either use the provided [AccessToken](./src/Oauth/AccessToken.php) or have your own that implements our [AccessTokenInterface](./src/Oauth/AccessTokenInterface.php).

### DemoSalesforceClient

```php
<?php
namespace App;

use Jmondi\Restforce\Oauth\AccessToken;
use Jmondi\Restforce\Oauth\AccessTokenInterface;
use Jmondi\Restforce\Oauth\SalesforceProvider;
use Jmondi\Restforce\Oauth\SalesforceProviderInterface;
use Jmondi\Restforce\RestClient\GuzzleRestClient;
use Jmondi\Restforce\RestClient\RestClientInterface;
use Jmondi\Restforce\RestforceClient;
use Jmondi\Restforce\SalesforceRequestClient;
use Jmondi\Restforce\TokenRefreshCallbackInterface;

class DemoSalesforceClient implements TokenRefreshCallbackInterface
{
    public function getRestforceClient():RestforceClient
    {
        if (empty($this->restforce)) {
            $this->restforce = RestforceClient::withDefaults(
                ACCESS_TOKEN,
                REFRESH_TOKEN,
                INSTANCE_URL,
                RESOURCE_OWNER_URL,
                CLIENT_ID,
                CLIENT_SECRET,
                REDIRECT_URL,
                $this
            );
        }
        return $this->restforce;
    }

    public function redirectToSalesforceAuth()
    {
        $provider = $this->getSalesforceProvider();
        $authorizationUrl = $provider->getAuthorizationUrl();
        header('Location: ' . $authorizationUrl);
        exit;
    }

    public function generateAccessTokenFromCode(string $code):AccessTokenInterface
    {
        $provider = $this->getSalesforceProvider();

        try {
            $accessToken = $provider->getAccessToken('authorization_code', [
                'code' => $code
            ]);
        } catch (IdentityProviderException $e) {
            exit($e->getMessage());
        }

        // STORE THE $accessToken TO PERSISTANCE LAYER

        return $accessToken;
    }

    public function tokenRefreshCallback(AccessToken $token)
    {
        // CALLBACK FUNCTION TO STORE THE REFRESHED $token TO PERSISTANCE LAYER
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
$limits = $restforce->limits();
// $limits = { ... }
```


#### UserInfo

[Docs](https://developer.salesforce.com/docs/atlas.en-us.api_rest.meta/api_rest/resources_limits.htm?search_text=limits) Get info about the logged-in user.

`public function limits():stdClass`

```php
<?php
$demoSalesforceClient = new DemoSalesforceClient();
$restforce = $demoSalesforceClient->getClient();
$user = $restforce->userInfo();
// $user = { ... }
```

#### Query

[Docs](https://developer.salesforce.com/docs/atlas.en-us.api_rest.meta/api_rest/dome_query.htm) Use the Query resource to execute a SOQL query that returns all the results in a single response.

`public function query(string $query):stdClass`

```php
<?php
$demoSalesforceClient = new DemoSalesforceClient();
$restforce = $demoSalesforceClient->getClient();
$results = $restforce->query('SELECT Id, Name FROM Account');
// $results = { ... }
```

#### QueryAll

[Docs](https://developer.salesforce.com/docs/atlas.en-us.api_rest.meta/api_rest/dome_queryall.htm) Include SOQL that includes deleted items.

`public function queryAll(string $query):stdClass`

```php
<?php
$demoSalesforceClient = new DemoSalesforceClient();
$restforce = $demoSalesforceClient->getClient();
$results = $restforce->queryAll('SELECT Id, Name FROM Account');
// $results = { ... }
```

#### Explain
[Docs](https://developer.salesforce.com/docs/atlas.en-us.api_rest.meta/api_rest/dome_query_explain.htm) Get feedback on how Salesforce will execute your query, report, or list view.

`public function explain(string $query):stdClass`

```php
<?php
$demoSalesforceClient = new DemoSalesforceClient();
$restforce = $demoSalesforceClient->getClient();
$explaination = $restforce->explain('SELECT Id, Name FROM Account');
// $explaination = { ... }
```

#### Find

[Docs](https://developer.salesforce.com/docs/atlas.en-us.api_rest.meta/api_rest/dome_get_field_values.htm?search_text=limits) Find resource `$id` of `$sobject`, optionally specify the fields you want to retrieve in the fields parameter and use the GET method of the resource.

`public function find(string $sobject, string $id, array $fields = []):stdClass`

```php
<?php
$demoSalesforceClient = new DemoSalesforceClient();
$restforce = $demoSalesforceClient->getClient();
$object = $restforce->find('Account', '001410000056Kf0AAE');
// $object = { ... }
```

#### Describe

[Docs](https://developer.salesforce.com/docs/atlas.en-us.api_rest.meta/api_rest/resources_sobject_describe.htm?search_text=describe) Completely describes the individual metadata at all levels for the specified object.

`public function describe(string $sobject):stdClass`

```php
<?php
$demoSalesforceClient = new DemoSalesforceClient();
$restforce = $demoSalesforceClient->getClient();
$description = $restforce->describe('Account');
// $description = { ... }
```

#### Picklist Values

[Docs](https://developer.salesforce.com/docs/atlas.en-us.api_rest.meta/api_rest/resources_sobject_describe.htm?search_text=describe) Uses the [describe](#describe) endpoint and extracts out the picklist values for the specified object and field.

`public function picklistValues(string $sobject, string $field):array`

```php
<?php
$demoSalesforceClient = new DemoSalesforceClient();
$restforce = $demoSalesforceClient->getClient();
$picklistValues = $restforce->describe('Task', 'Type');
// $picklistValues = { ... }
```


#### Create

[Docs](https://developer.salesforce.com/docs/atlas.en-us.api_rest.meta/api_rest/dome_sobject_create.htm) Create new records of `$sobject`. The response body will contain the ID of the created record if the call is successful.

`public function create(string $sobject, array $data):stdClass`

```php
<?php
$demoSalesforceClient = new DemoSalesforceClient();
$restforce = $demoSalesforceClient->getClient();
$id = $restforce->create('Account', [
    'Name' => 'Foo Bar'
]);
// $id = '001i000001ysdBGAAY'` 
```

#### Update

[Docs](https://developer.salesforce.com/docs/atlas.en-us.api_rest.meta/api_rest/dome_update_fields.htm?search_text=describe) You use the SObject Rows resource to update records. The response will be the a bool of `$success`.

`public function update(string $sobject, string $id, array $data):bool`

```php
<?php
$demoSalesforceClient = new DemoSalesforceClient();
$restforce = $demoSalesforceClient->getClient();
$success = $restforce->update('Account', '001i000001ysdBGAAY', [
    'Name' => 'Foo Bar Two'
]);
// $success = true|false
```

#### Destroy

[Docs](https://developer.salesforce.com/docs/atlas.en-us.api_rest.meta/api_rest/dome_delete_record.htm?search_text=describe) Delete record of `$sobject` and `$id`. The response will be the a bool of `$success`.

`public function destroy(string $sobject, string $id):bool`

```php
<?php
$demoSalesforceClient = new DemoSalesforceClient();
$restforce = $demoSalesforceClient->getClient();
$success = $restforce->destroy('Account', '001i000001ysdBGAAY');
// $success = true|false
```

## Contributing

Thanks for considering contributing to our Restforcephp project. Just a few things:
 
- Make sure your commit conforms to the PSR-2 coding standard.
- Make sure your commit messages are well defined.
- Make sure you have added the necessary unit tests for your changes.
- Run _all_ the tests to assure nothing else was accidentally broken.
- Submit a pull request.

#### Unit Tests:

```bash
$ vendor/bin/phpunit
```

##### With Code Coverage:

```bash
$ vendor/bin/phpunit --coverage-text --coverage-html coverage_report
```

#### Check PHP-CS PSR2 Test:

```bash
$ vendor/bin/phpcs -p --standard=PSR2 src/ tests/
```

#### Apply PHP-CS PSR2 Fix:

Auto runs and resolves some low hanging PSR2 fixes, this might not get all of them, so rerun the check after.

```bash
$ vendor/bin/phpcbf --standard=PSR2 src/ tests/ 
```
