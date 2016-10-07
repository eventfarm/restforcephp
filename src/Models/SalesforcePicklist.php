<?php
namespace Jmondi\Restforce\Models;

class SalesforcePicklist
{
    /**
     * @var array
     */
    private $fields;
    /**
     * @var string
     */
    private $field;

    public function __construct(array $fields, string $field)
    {
        $this->fields = $fields;
        $this->field = $field;
    }

    public function extract():array
    {
        foreach ($this->fields as $field) {
            if (isset($field->name) && $field->name === $this->field && isset($field->picklistValues)) {
                return $field->picklistValues;
            }
        }

        throw new SalesforcePicklistFieldDoesNotExist($this->field . " is not a dependent picklist");
    }
}
