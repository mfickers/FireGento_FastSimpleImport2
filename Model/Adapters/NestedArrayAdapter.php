<?php
/**
 * Copyright © 2016 FireGento e.V. - All rights reserved.
 * See LICENSE.md bundled with this module for license details.
 */

namespace FireGento\FastSimpleImport\Model\Adapters;

class NestedArrayAdapter extends ArrayAdapter
{
    private string $multipleValueSeparator;

    public function __construct(array $data, string $multipleValueSeparator = ', ')
    {
        $this->multipleValueSeparator = $multipleValueSeparator;

        parent::__construct($data);

        foreach ($this->array as &$row) {
            foreach ($row as &$value) {
                if (is_array($value)) {
                    $this->convertToArray($value);
                }
            }
        }
    }

    /**
     * Transform nested array to string
     */
    private function convertToArray(array &$line)
    {
        $implodeStr = $this->multipleValueSeparator;
        $array = array_map(
            function ($value, $key) use (&$implodeStr) {
                if (is_array($value) && is_numeric($key)) {
                    $this->convertToArray($value);
                    $implodeStr = '|';
                    return $value;
                }
                return sprintf("%s=%s", $key, $value);
            },
            $line,
            array_keys($line)
        );

        $line = implode($implodeStr, $array);
    }
}
