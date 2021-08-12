<?php

namespace EventFarm\Restforce\Types;

/**
 * Class Account Type Salesforce
 *
 * @package EventFarm\Restforce\Types
 * @author  Florian Duc <fduc@voyageprive.com>
 */
class Account
{
    const SF_OBJECT = 'Account';

    const SF_ACCOUNT_ID = 'Id';
    const SF_ACCOUNT_NAME = 'Name';
    const SF_ACCOUNT_COUNTRY = 'Account_Country__c';
    const SF_ACCOUNT_CITY = 'Account_City__c';
    const SF_ACCOUNT_STREET = 'Account_Street__c';
    const SF_ACCOUNT_ZIPCODE = 'Account_Zip_Postal_code__c';

    /**
     * Query fields available in SF for an Account
     */
    const SF_FIELD_LIST = [
        self::SF_ACCOUNT_ID,
        self::SF_ACCOUNT_NAME,
        self::SF_ACCOUNT_COUNTRY,
        self::SF_ACCOUNT_CITY,
        self::SF_ACCOUNT_STREET,
        self::SF_ACCOUNT_ZIPCODE
    ];
}