<?php
declare(strict_types=1);

namespace Propeller\Includes\Object;

if ( ! defined( 'ABSPATH' ) ) exit;

class SparePart extends BaseObject {
    public array $images = [];
    public ?Product $product = null;

    public function __construct(object $part) {
        parent::__construct($part);

        if (isset($part->product))
            $this->product = new Product($part->product);
    }
}
