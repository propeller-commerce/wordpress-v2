<?php
declare(strict_types=1);

namespace Propeller\Includes\Object;

if ( ! defined( 'ABSPATH' ) ) exit;

use stdClass;

class Attribute extends BaseObject {
    const ATTR_TEXT         = 'TEXT';
    const ATTR_ENUM         = 'ENUM';
    const ATTR_COLOR        = 'COLOR';
    const ATTR_DATETIME     = 'DATETIME';
    const ATTR_INTEGER      = 'INT';
    const ATTR_DECIMAL      = 'DECIMAL';

    public stdClass $attributeDescription;
    public stdClass $value;

    public function __construct(object $attr) {
        parent::__construct($attr);
    }

    public function has_value(): bool {
        switch ($this->get_type()) {
            case self::ATTR_TEXT:
                return $this->hasTextValue();
            case self::ATTR_ENUM:
                return $this->hasEnumValue();
            case self::ATTR_INTEGER:
                return $this->hasIntValue();
            case self::ATTR_DECIMAL:
                return $this->hasDecimalValue();
            case self::ATTR_DATETIME:
                return $this->hasDateTimeValue();
            case self::ATTR_COLOR:
                return $this->hasColorValue();
        }

        return false;
    }

    public function get_type(): string {
        return (string)$this->attributeDescription->type;
    }

    public function get_name(): string {
        return (string)$this->attributeDescription->name;
    }

    public function get_description(): string {
        $found = array_filter($this->attributeDescription->descriptions, function($obj) { return strtolower($obj->language) == strtolower(PROPELLER_LANG); });

        if (!count($found))
            $found = array_filter($this->attributeDescription->descriptions, function($obj) { return strtolower($obj->language) == strtolower(PROPELLER_FALLBACK_LANG); });

        if (count($found)) {
            $item = current($found);
            return $item !== false ? (string)$item->value : '';
        }

        return '';
    }

    public function is_searchable(): bool {
        return (bool)$this->attributeDescription->isSearchable;
    }

    public function is_public(): bool {
        return (bool)$this->attributeDescription->isPublic;
    }

    public function is_hidden(): bool {
        return (bool)$this->attributeDescription->isHidden;
    }

    public function get_value(): mixed {
        switch ($this->get_type()) {
            case self::ATTR_TEXT:
                return $this->getTextValue();
            case self::ATTR_ENUM:
                return $this->getEnumValue();
            case self::ATTR_INTEGER:
                return $this->getIntValue();
            case self::ATTR_DECIMAL:
                return $this->getDecimalValue();
            case self::ATTR_DATETIME:
                return $this->getDateTimeValue();
            case self::ATTR_COLOR:
                return $this->getColorValue();
        }

        return null;
    }

    // color attr
    private function hasColorValue(): bool {
        return isset($this->value->colorValue) && !empty($this->value->colorValue);
    }

    private function getColorValue(): string {
        if ($this->hasColorValue())
            return (string)$this->value->colorValue;

        return '';
    }

    // int attr
    private function hasIntValue(): bool {
        return isset($this->value->intValue) && !empty($this->value->intValue) && is_numeric($this->value->intValue);
    }

    private function getIntValue(): int|string {
        if ($this->hasIntValue())
            return (int) $this->value->intValue;

        return '';
    }

    // decimal attr
    private function hasDecimalValue(): bool {
        return isset($this->value->decimalValue) && !empty($this->value->decimalValue) && is_numeric($this->value->decimalValue);
    }

    private function getDecimalValue(): float|string {
        if ($this->hasDecimalValue())
            return (float) $this->value->decimalValue;

        return '';
    }

    // date attr
    private function hasDateTimeValue(): bool {
        return isset($this->value->dateTimeValue) && strlen((string)$this->value->dateTimeValue) > 0;
    }

    private function getDateTimeValue(): string {
        $date_format = (string)get_option('date_format');
        $time_format = (string)get_option('time_format');
        $timestamp = strtotime((string)$this->value->dateTimeValue);

        return $timestamp !== false ? date_i18n($date_format . $time_format, $timestamp) : '';
    }

    // text attr
    private function hasTextValue(): bool {
        return isset($this->value->textValues) && is_array($this->value->textValues) && sizeof($this->value->textValues) &&
                isset($this->value->textValues[0]->values) && sizeof($this->value->textValues[0]->values) && strlen((string)$this->value->textValues[0]->values[0]);
    }

    private function getTextValue(): string {
        $found = array_filter($this->value->textValues, function($obj) {
            return strtolower($obj->language) == strtolower(PROPELLER_LANG) &&
                   isset($obj->values) && !is_null($obj->values) && count($obj->values) && $this->has_array_values($obj->values);
        });

        if (!count($found))
            $found = array_filter($this->value->textValues, function($obj) {
                return strtolower($obj->language) == strtolower(PROPELLER_FALLBACK_LANG) &&
                       isset($obj->values) && !is_null($obj->values) && count($obj->values) && $this->has_array_values($obj->values);
            });

        if (count($found)) {
            $item = current($found);
            return $item !== false ? implode(', ', $item->values) : '';
        }

        return '';
    }

    private function has_array_values(array $vals_arr): bool {
        foreach ($vals_arr as $val) {
            if (!empty($val))
                return true;
        }

        return false;
    }

    // enum attr
    private function hasEnumValue(): bool {
        return !empty($this->value->enumValues) && is_array($this->value->enumValues) && sizeof($this->value->enumValues) && $this->has_array_values($this->value->enumValues);
    }

    private function getEnumValue(): string {
        if ($this->hasEnumValue()) {
            $values = [];

            foreach ($this->value->enumValues as $val) {
                if (!empty(trim((string)$val)))
                    $values[] = $val;
            }

            return join(', ', $values);
        }

        return '';
    }
}
