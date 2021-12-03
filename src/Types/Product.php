<?php

namespace EventFarm\Restforce\Types;

/**
 * Class Product Type Salesforce
 *
 * @package EventFarm\Restforce\Types
 * @author  Florian Duc <fduc@voyageprive.com>
 */
class Product
{
    const SF_OBJECT = 'Product__c';

    const SF_ID = 'Id';
    const SF_NAME = 'Name';
    const SF_COUNTRY = 'Product_Country__c';
    const SF_CITY = 'Product_City__c';
    const SF_STREET = 'Product_Street__c';
    const SF_ZIPCODE = 'Product_Zip_Postal_code__c';
    const SF_STATUS = 'Validation_Status__c';
    const SF_RECORD_TYPE = 'RecordTypeId';
    const SF_CAPACITY = 'Capacity__c';
    const SF_TYPE = 'RecordTypeId';
    const SF_FRONT_EMAIL = 'Front_Desk_Email__c';
    const SF_FRONT_PHONE = 'Front_Desk_Phone__c';
    const SF_COORDINATE = 'GPS_coordinates__c';
    const SF_CATEGORY = 'Category__c';
    const SF_ATH_ID = 'Product_ATH_id__c';
    const SF_GPS_LAT = 'GPS_coordinates__Latitude__s';
    const SF_GPS_LNG = 'GPS_coordinates__Longitude__s';
    const SF_ACCOUNT = 'Account__c';
    const SF_BOOKING_URL = 'Booking_or_Trip__c';
    const SF_TRIPADVISOR_URL = 'Tripadvisor_link__c';
    const SF_BOOKING_SCORE = 'Market_Score__c';
    const SF_TRIPDVISOR_SCORE = 'Market_Score_TripAdvisor__c';
    const SF_ROOMING_LIST_CONTACT = 'Number_of_Rooming_List_Contact__c';

    const SF_PENDING_STATUS = 'Pending';
    const SF_ACTIVE_STATUS = 'Approved';
    const SF_INACTIVE_STATUS = 'Refused';

    /**
     * Query fields available in SF for a Product
     */
    const SF_FIELD_LIST = [
        self::SF_ID,
        self::SF_NAME,
        self::SF_COUNTRY,
        self::SF_CITY,
        self::SF_STREET,
        self::SF_ZIPCODE,
        self::SF_STATUS,
        self::SF_CAPACITY,
        self::SF_TYPE,
        self::SF_FRONT_EMAIL,
        self::SF_FRONT_PHONE,
        self::SF_COORDINATE,
        self::SF_CATEGORY
    ];
}