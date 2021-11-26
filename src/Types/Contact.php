<?php

namespace EventFarm\Restforce\Types;

/**
 * Class Contact Salesforce
 *
 * @package EventFarm\Restforce\Types
 * @author  Nils Peritore <nperitore@voyageprive.com>
 */
class Contact
{
    const SF_OBJECT = 'Contact';

    const SF_ID = 'Id';
    const SF_FIRST_NAME = 'FirstName';
    const SF_LAST_NAME = 'LastName';
    const SF_EMAIL = 'Email';
    const SF_PHONE = 'Phone';

    /**
     * Query fields available in SF for a Contact
     */
    const SF_FIELD_LIST = [
        self::SF_ID,
        self::SF_FIRST_NAME,
        self::SF_LAST_NAME,
        self::SF_EMAIL,
        self::SF_PHONE
    ];
}