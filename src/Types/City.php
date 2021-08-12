<?php

namespace EventFarm\Restforce\Types;

/**
 * Class City Type Salesforce
 *
 * @package EventFarm\Restforce\Types
 * @author  Florian Duc <fduc@voyageprive.com>
 */
class City
{
    const SF_OBJECT = 'City__c';

    const SF_ACCOUNT_ID = 'Id';
    const SF_ACCOUNT_NAME = 'Name';

    /**
     * Query fields available in SF for a City
     */
    const SF_FIELD_LIST = [
        self::SF_ACCOUNT_ID,
        self::SF_ACCOUNT_NAME,
    ];
}