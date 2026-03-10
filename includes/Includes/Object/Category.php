<?php
declare(strict_types=1);

namespace Propeller\Includes\Object;

if ( ! defined( 'ABSPATH' ) ) exit;

use stdClass;

class Category extends BaseObject {

    public mixed $categoryId = null;
    public ?array $name = null;
    public array $attributes = [];
    public array $trackAttributes = [];
    public ?stdClass $attributeItems = null;
    public ?stdClass $trackAttributeItems = null;
    public array $images = [];

    public function __construct(object $category) {
        parent::__construct($category);

        if (isset($category->trackAttributes) && isset($category->trackAttributes->itemsFound) && $category->trackAttributes->itemsFound > 0) {
            $attrs = new AttributeArray($category->trackAttributes->items);

            $this->trackAttributes = $attrs->get_non_empty_attrs();

            $this->trackAttributeItems = new stdClass();

            $this->trackAttributeItems->itemsFound = $category->trackAttributes->itemsFound;
            $this->trackAttributeItems->offset = $category->trackAttributes->offset;
            $this->trackAttributeItems->page = $category->trackAttributes->page;
            $this->trackAttributeItems->pages = $category->trackAttributes->pages;
            $this->trackAttributeItems->start = $category->trackAttributes->start;
            $this->trackAttributeItems->end = $category->trackAttributes->end;
        }
    }

    public function has_attributes(): bool {
        return count($this->attributes) > 0;
    }

    public function get_attributes(): array {
        return $this->attributes;
    }

    public function has_track_attributes(): bool {
        return count($this->trackAttributes) > 0;
    }

    public function get_track_attributes(): array {
        return $this->trackAttributes;
    }

    public function get_track_attr_by_name(string $name): ?Attribute {
        $found = array_filter($this->trackAttributes, function($obj) use ($name) {
            return $obj->attributeDescription->name == $name;
        });

        if (count($found))
            return current($found);

        return null;
    }

    public function get_track_attr_value(string $name): mixed {
        $found = array_filter($this->trackAttributes, function($obj) use ($name) {
            return $obj->attributeDescription->name == $name;
        });

        if (count($found)) {
            $item = current($found);
            return $item !== false ? $item->get_value() : null;
        }

        return null;
    }
}
