<?php

namespace Jmondi\Restforce\Models;

class SalesforcePicklistTest extends \PHPUnit_Framework_TestCase
{
    public function testSalesforcePicklistExtractsCorrectValuesForField()
    {
        $fieldsJSON = '[
            {
                "name": "Subject",
                "picklistValues": [
                    {
                        "active": true,
                        "defaultValue": false,
                        "label": "Call",
                        "validFor": null,
                        "value": "Call"
                    },
                    {
                        "active": true,
                        "defaultValue": false,
                        "label": "Email",
                        "validFor": null,
                        "value": "Email"
                    }
                ]
            },
            {
                "name": "SubjectTwp",
                "picklistValues": [
                    {
                        "active": true,
                        "defaultValue": false,
                        "label": "CallTwo",
                        "validFor": null,
                        "value": "CallTwo"
                    }
                ]
            }
        ]';
        $fields = json_decode($fieldsJSON);
        $field = 'Subject';

        $salesforcePicklist = new SalesforcePicklist($fields, $field);

        $extractedPicklist = $salesforcePicklist->extract();

        $this->assertSame(2, count($extractedPicklist));
        $this->assertSame("Call", $extractedPicklist[0]->value);
        $this->assertSame("Email", $extractedPicklist[1]->value);
    }

    public function testSalesforcePicklistThrowExceptionWhenNoFieldsInList()
    {
        $fieldsJSON = '[]';
        $fields = json_decode($fieldsJSON);
        $field = 'Subject';

        $salesforcePicklist = new SalesforcePicklist($fields, $field);

        $this->expectException(SalesforcePicklistFieldDoesNotExist::class);

        $extractedPicklist = $salesforcePicklist->extract();
    }

    public function testSalesforcePicklistThrowExceptionWhenFieldNameNotInList()
    {
        $fieldsJSON = '[
            {
                "name": "Subject",
                "picklistValues": [
                    {
                        "active": true,
                        "defaultValue": false,
                        "label": "Call",
                        "validFor": null,
                        "value": "Call"
                    },
                    {
                        "active": true,
                        "defaultValue": false,
                        "label": "Email",
                        "validFor": null,
                        "value": "Email"
                    }
                ]
            }
        ]';
        $fields = json_decode($fieldsJSON);
        $field = 'SubjectTwo';

        $salesforcePicklist = new SalesforcePicklist($fields, $field);

        $this->expectException(SalesforcePicklistFieldDoesNotExist::class);

        $extractedPicklist = $salesforcePicklist->extract();
    }

    public function testSalesforcePicklistThrowExceptionWhenPicklistValueDoesNotExist()
    {
        $fieldsJSON = '[
            {
                "name": "Subject"
            }
        ]';
        $fields = json_decode($fieldsJSON);
        $field = 'Subject';

        $salesforcePicklist = new SalesforcePicklist($fields, $field);

        $this->expectException(SalesforcePicklistFieldDoesNotExist::class);

        $extractedPicklist = $salesforcePicklist->extract();
    }
}
