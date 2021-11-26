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

    const SF_ID = 'Id';
    const SF_NAME = 'Name';
    const SF_COUNTRY = 'Country__c';
    const SF_COUNTRY_NAME = 'Country__r.Name';

    /**
     * Query fields available in SF for a City
     */
    const SF_FIELD_LIST = [
        self::SF_ID,
        self::SF_NAME,
        self::SF_COUNTRY,
    ];
}