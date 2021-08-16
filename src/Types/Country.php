<?php

namespace EventFarm\Restforce\Types;

/**
 * Class Country Type Salesforce
 *
 * @package EventFarm\Restforce\Types
 * @author  Florian Duc <fduc@voyageprive.com>
 */
class Country
{
    const SF_OBJECT = 'Country__c';

    const SF_ID = 'Id';
    const SF_NAME = 'Name';

    /**
     * Query fields available in SF for a City
     */
    const SF_FIELD_LIST = [
        self::SF_ID,
        self::SF_NAME,
    ];
}