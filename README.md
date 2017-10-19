# Restforce PHP

[![Travis](https://img.shields.io/travis/eventfarm/restforcephp.svg?maxAge=2592000?style=flat-square)](https://travis-ci.org/eventfarm/restforcephp)
[![Downloads](https://img.shields.io/packagist/dt/eventfarm/restforcephp.svg?style=flat-square)](https://packagist.org/packages/eventfarm/restforcephp)
[![Packagist](https://img.shields.io/packagist/l/eventfarm/restforcephp.svg?maxAge=2592000?style=flat-square)](https://packagist.org/packages/eventfarm/restforcephp)
[![Code Climate](https://codeclimate.com/github/eventfarm/restforcephp/badges/gpa.svg)](https://codeclimate.com/github/eventfarm/restforcephp)
[![Test Coverage](https://codeclimate.com/github/eventfarm/restforcephp/badges/coverage.svg)](https://codeclimate.com/github/eventfarm/restforcephp/coverage)

This is meant to emulate what the [ejhomes/restforce gem](https://github.com/ejholmes/restforce) is doing for rails.

## Installation

This library requires PHP 7.1 or later; we recommend using the latest available version of PHP.

```
$ composer require eventfarm/restforcephp
```

Or.

Add the following lines to your ``composer.json`` file.

```json
{
    "require": {
        "eventfarm/restforcephp": "^2.0.0"
    }
}
```

```bash
$ composer install
```

## Project Defaults

```php
<?php
namespace App;

use EventFarm\Restforce\Rest\OAuthAccessToken;
use EventFarm\Restforce\Restforce;
use EventFarm\Restforce\RestforceInterface;

class DemoSalesforceApi
{
    /** @var null|RestforceInterface $restforce */
    private $restforce;
    
    public function getRestforceClient(): RestforceInterface
    {
        if ($this->restforce === null) {
            // You need either the OAuthAccessToken
            // or the Username & Password,
            // the other(s) can be null.
            $this->restforce = new Restforce(
                getenv('SF_CLIENT_ID'),
                getenv('SF_CLIENT_SECRET'),
                new OAuthAccessToken(...),
                getenv('SF_USERNAME'),
                getenv('SF_PASSWORD')
            );
        }
        return $this->restforce;
    }
}
```

## Access Token Information

#### OAuth Scopes

Consult the [Salesforce OAuth 2.0 Documentation](https://developer.salesforce.com/page/Digging_Deeper_into_OAuth_2.0_on_Force.com#Configuring_OAuth_2.0_Access_for_your_Application) to find out what Available OAuth Scopes your app needs.

## Salesforce Documentation

Links to Salesforce documentation pages can be found in each section. Alternatively, here is the [holy grail of the Saleforce endpoints.](https://developer.salesforce.com/docs/atlas.en-us.api_rest.meta/api_rest/resources_list.htm) 

## Usage

#### Limits

[Docs](https://developer.salesforce.com/docs/atlas.en-us.api_rest.meta/api_rest/resources_limits.htm?search_text=limits) Returns a list of daily API limits for the salesforce api. Refer to the docs for the full list of options.

`public function limits(): \Psr\Http\Message\ResponseInterface`

```php
<?php
/** @var \EventFarm\Restforce\RestforceInterface $restforce */
$restforce = (new DemoSalesforceApi())->getClient();
/** @var \Psr\Http\Message\ResponseInterface $responseInterface */
$responseInterface = $restforce->limits();
```


#### UserInfo

[Docs](https://developer.salesforce.com/docs/atlas.en-us.mobile_sdk.meta/mobile_sdk/oauth_using_identity_urls.htm) Get info about the logged-in user.

`public function limits(): \Psr\Http\Message\ResponseInterface`

```php
<?php
/** @var \EventFarm\Restforce\RestforceInterface $restforce */
$restforce = (new DemoSalesforceApi())->getClient();
/** @var \Psr\Http\Message\ResponseInterface $responseInterface */
$responseInterface = $restforce->userInfo();
```

#### Query

[Docs](https://developer.salesforce.com/docs/atlas.en-us.api_rest.meta/api_rest/dome_query.htm) Use the Query resource to execute a SOQL query that returns all the results in a single response.

`public function query(string $query): \Psr\Http\Message\ResponseInterface`

```php
<?php
/** @var \EventFarm\Restforce\RestforceInterface $restforce */
$restforce = (new DemoSalesforceApi())->getClient();
/** @var \Psr\Http\Message\ResponseInterface $responseInterface */
$responseInterface = $restforce->query('SELECT Id, Name FROM Account');
```

#### Find

[Docs](https://developer.salesforce.com/docs/atlas.en-us.api_rest.meta/api_rest/dome_get_field_values.htm?search_text=limits) Find resource `$id` of `$sobject`, optionally specify the fields you want to retrieve in the fields parameter and use the GET method of the resource.

`public function find(string $sobject, string $id, array $fields = []): \Psr\Http\Message\ResponseInterface`

```php
<?php
/** @var \EventFarm\Restforce\RestforceInterface $restforce */
$restforce = (new DemoSalesforceApi())->getClient();
/** @var \Psr\Http\Message\ResponseInterface $responseInterface */
$responseInterface= $restforce->find('Account', '001410000056Kf0AAE');
```

#### Describe

[Docs](https://developer.salesforce.com/docs/atlas.en-us.api_rest.meta/api_rest/resources_sobject_describe.htm?search_text=describe) Completely describes the individual metadata at all levels for the specified object.

`public function describe(string $sobject): \Psr\Http\Message\ResponseInterface`

```php
<?php
/** @var \EventFarm\Restforce\RestforceInterface $restforce */
$restforce = (new DemoSalesforceApi())->getClient();
/** @var \Psr\Http\Message\ResponseInterface $responseInterface */
$responseInterface = $restforce->describe('Account');
```

#### Create

[Docs](https://developer.salesforce.com/docs/atlas.en-us.api_rest.meta/api_rest/dome_sobject_create.htm) Create new records of `$sobject`. The response body will contain the ID of the created record if the call is successful.

`public function create(string $sobject, array $data): \Psr\Http\Message\ResponseInterface`

```php
<?php
/** @var \EventFarm\Restforce\RestforceInterface $restforce */
$restforce = (new DemoSalesforceApi())->getClient();
/** @var \Psr\Http\Message\ResponseInterface $responseInterface */
$responseInterface = $restforce->create('Account', [
    'Name' => 'Foo Bar'
]);
```

#### Update

[Docs](https://developer.salesforce.com/docs/atlas.en-us.api_rest.meta/api_rest/dome_update_fields.htm?search_text=describe) You use the SObject Rows resource to update records. The response will be the a bool of `$success`.

`public function update(string $sobject, string $id, array $data):bool`

```php
<?php
/** @var \EventFarm\Restforce\RestforceInterface $restforce */
$restforce = (new DemoSalesforceApi())->getClient();
/** @var \Psr\Http\Message\ResponseInterface $responseInterface */
$responseInterface = $restforce->update('Account', '001i000001ysdBGAAY', [
    'Name' => 'Foo Bar Two'
]);
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
