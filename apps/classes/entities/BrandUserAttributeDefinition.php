<?php

AAFW::import('jp.aainc.aafw.base.aafwEntityBase');

class BrandUserAttributeDefinition extends aafwEntityBase {

    private $arrayedJsonValueSet;

    public function convertValueByValueSet($value) {
        if ($this->attribute_type !== BrandUserAttributeDefinitions::ATTRIBUTE_TYPE_SET) {
            throw new aafwException("The object can't execute the method because of the attribute_type value: id=" . $this->id);
        }

        if ($this->value_set === '') {
            return $value;
        }

        if ($this->arrayedJsonValueSet === null) {
            $this->arrayedJsonValueSet = json_decode($this->value_set, true);
        }

        $convertedValue = $this->arrayedJsonValueSet[$value];
        return $convertedValue ?: null;
    }
}
