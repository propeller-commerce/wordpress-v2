<?php
declare(strict_types=1);

namespace Propeller\Includes\Object;

if ( ! defined( 'ABSPATH' ) ) exit;

use stdClass;

class Filter extends BaseObject {
    private const ATTR_TEXT         = 'TEXT';
    private const ATTR_ENUM         = 'ENUM';
    private const ATTR_COLOR        = 'COLOR';
    private const ATTR_DATETIME     = 'DATETIME';
    private const ATTR_INTEGER      = 'INT';
    private const ATTR_DECIMAL      = 'DECIMAL';

    public ?string $type = null;
    public ?array $textFilters = null;
    public ?stdClass $integerRangeFilter = null;
    public ?stdClass $decimalRangeFilter = null;
    public ?string $subtype = null;

    public function __construct(object $attr) {
        parent::__construct($attr);

        $this->set_subtype();
    }

    public function has_value(): bool {
        switch ($this->get_type()) {
            case self::ATTR_TEXT:
            case self::ATTR_COLOR:
            case self::ATTR_DATETIME:
            case self::ATTR_ENUM:
                return $this->hasTextValue();
            case self::ATTR_INTEGER:
                return $this->hasIntValue();
            case self::ATTR_DECIMAL:
                return $this->hasDecimalValue();
        }

        return false;
    }

    public function get_value(): mixed {
        switch ($this->get_type()) {
            case self::ATTR_TEXT:
            case self::ATTR_COLOR:
            case self::ATTR_DATETIME:
            case self::ATTR_ENUM:
                return $this->getTextValue();
            case self::ATTR_INTEGER:
                return $this->getIntValue();
            case self::ATTR_DECIMAL:
                return $this->getDecimalValue();
        }

        return null;
    }

    public function get_type(): ?string {
        return $this->type;
    }

    public function get_subtype(): ?string {
        switch ($this->get_type()) {
            case self::ATTR_TEXT:
            case self::ATTR_COLOR:
            case self::ATTR_DATETIME:
            case self::ATTR_ENUM:
                return 'text';
            case self::ATTR_INTEGER:
            case self::ATTR_DECIMAL:
                return 'range';
        }

        return null;
    }

    private function set_subtype(): void {
        switch ($this->get_type()) {
            case self::ATTR_TEXT:
            case self::ATTR_COLOR:
            case self::ATTR_DATETIME:
            case self::ATTR_ENUM:
                $this->subtype = 'text';
                break;
            case self::ATTR_INTEGER:
            case self::ATTR_DECIMAL:
                $this->subtype = 'range';
                break;
        }
    }

    // int attr
    private function hasIntValue(): bool {
        return $this->integerRangeFilter !== null;
    }

    private function getIntValue(): array {
        return [
            'min' => $this->integerRangeFilter->min,
            'max' => $this->integerRangeFilter->max
        ];
    }

    // decimal attr
    private function hasDecimalValue(): bool {
        return $this->decimalRangeFilter !== null;
    }

    private function getDecimalValue(): array {
        return [
            'min' => $this->decimalRangeFilter->min,
            'max' => $this->decimalRangeFilter->max
        ];
    }

    // text attr
    private function hasTextValue(): bool {
        return is_array($this->textFilters) && sizeof($this->textFilters) > 0;
    }

    private function getTextValue(): ?array {
        return $this->textFilters;
    }
}
