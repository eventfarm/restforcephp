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

    const SF_ID = 'Id';
    const SF_NAME = 'Name';
    const SF_COUNTRY = 'Account_Country__c';
    const SF_CITY = 'Account_City__c';
    const SF_STREET = 'Account_Street__c';
    const SF_ZIPCODE = 'Account_Zip_Postal_code__c';
    const SF_STATUS = 'Global_Account_Status';

    const SF_ACTIVE_STATUS = 'Active Account';
    const SF_INACTIVE_STATUS = 'Inactive Account';

    /**
     * Query fields available in SF for an Account
     */
    const SF_FIELD_LIST = [
        self::SF_ID,
        self::SF_NAME,
        self::SF_COUNTRY,
        self::SF_CITY,
        self::SF_STREET,
        self::SF_ZIPCODE
    ];
}