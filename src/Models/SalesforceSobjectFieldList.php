<?php
namespace EventFarm\Restforce\Models;

class SalesforceSobjectFieldlist
{
    /** @var array */
    private $fields;

    public function __construct(array $fields)
    {
        $this->fields = $fields;
    }

    public function extract(): array
    {
        $fieldNames = [];

        foreach ($this->fields as $field) {
            $fieldNames[] = [
                'label' => $field->label,
                'name' => $field->name,
            ];
        }

        return $fieldNames;
    }
}
