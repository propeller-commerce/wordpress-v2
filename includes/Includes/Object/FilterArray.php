<?php
declare(strict_types=1);

namespace Propeller\Includes\Object;

if ( ! defined( 'ABSPATH' ) ) exit;

use Propeller\Includes\Object\Filter;

class FilterArray {
    public array $attributes;

    public function __construct(array $attr_array) {
        $this->attributes = [];

        foreach ($attr_array as $attr)
            $this->attributes[] = new Filter($attr);
    }

    public function get_non_empty_attrs(): array {
        $attrs = [];

        foreach ($this->attributes as $attr) {
            if ($attr->has_value())
                $attrs[] = $attr;
        }

        return $attrs;
    }
}
