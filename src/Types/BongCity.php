<?php

namespace EventFarm\Restforce\Types;

/**
 * Class City Type Salesforce
 *
 * @package EventFarm\Restforce\Types
 * @author  Florian Duc <fduc@voyageprive.com>
 */
class BongCity
{
    const SF_OBJECT = 'BongCity__c';

    const SF_ID = 'Id';
    const SF_NAME = 'Name';
    const SF_ID_BONG = 'ID_BONG__c';
    const SF_COUNTRY_NAME = 'CountryName__c';

    /**
     * Query fields available in SF for a BongCity
     */
    const SF_FIELD_LIST = [
        self::SF_ID,
        self::SF_NAME,
        self::SF_ID_BONG,
        self::SF_COUNTRY_NAME,
    ];
}