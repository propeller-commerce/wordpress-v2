<?php
declare(strict_types=1);

namespace Propeller\Includes\Object;

if ( ! defined( 'ABSPATH' ) ) exit;

class OrderItem extends BaseObject {
    public ?array $shipments = null;

    public mixed $id = null;
    public int $quantity = 0;

    public function __construct(object $order_item, ?array $shipments) {
        parent::__construct($order_item);

        $this->shipments = $shipments;
    }

    public function is_shipped(): bool {
        $item_id = $this->id;
        $quantity = $this->quantity;
        $shipped = 0;

        if (!is_array($this->shipments))
            return false;

        if (!count($this->shipments))
            return false;

        foreach ($this->shipments as $shipment) {
            $found = array_filter($shipment->items, function($obj) use($item_id) {
                return $obj->orderItemId == $item_id;
            });

            if (count($found))
                $shipped += current($found)->quantity;
        }

        if ($shipped == $quantity)
            return true;

        return false;
    }

    public function is_partially_shipped(): bool {
        $item_id = $this->id;
        $quantity = $this->quantity;
        $shipped = 0;

        if (!is_array($this->shipments))
            return false;

        if (!count($this->shipments))
            return false;

        foreach ($this->shipments as $shipment) {
            $found = array_filter($shipment->items, function($obj) use($item_id) {
                return $obj->orderItemId == $item_id;
            });

            if (count($found))
                $shipped += current($found)->quantity;
        }

        if ($shipped > 0 && $shipped < $quantity)
            return true;

        return false;
    }
}
