<?php
declare(strict_types=1);

namespace Propeller\Includes\Object;

if ( ! defined( 'ABSPATH' ) ) exit;

use Propeller\Includes\Object\Attribute;
use stdClass;

class AttributeArray {
    public array $attributeItems = [];
    public ?stdClass $attributes = null;

    public function __construct(stdClass $attr_response) {
        $this->attributeItems = [];
        $this->attributes = $attr_response;

        foreach ($attr_response->items as $attr)
            $this->attributeItems[] = new Attribute($attr);
    }

    public function get_non_empty_attrs(): stdClass {
        $attrs = [];

        foreach ($this->attributeItems as $attr) {
            if ($attr->has_value())
                $attrs[] = $attr;
        }

        $this->attributes->items = $attrs;

        return $this->attributes;
    }
}
