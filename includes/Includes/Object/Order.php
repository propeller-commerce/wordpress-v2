<?php
declare(strict_types=1);

namespace Propeller\Includes\Object;

if ( ! defined( 'ABSPATH' ) ) exit;

use stdClass;

class Order extends BaseObject {
    protected int $total_quantity = 0;
    public ?array $shipments = null;
    public ?array $items = null;
    protected ?stdClass $media = null;

    public function __construct(object $order) {
        parent::__construct($order);

        $order_items = [];

        if (isset($this->items)) {
            foreach ($this->items as $item) {
                $order_items[] = new OrderItem($item, $this->shipments);

                $this->total_quantity += $item->quantity;
            }

            $this->items = $order_items;
        }
    }

    public function is_open(): bool {
        if (!is_array($this->shipments))
            return true;
        else if (is_array($this->shipments) && !count($this->shipments))
            return true;

        return false;
    }

    public function is_partially_shipped(): bool {
        if (!is_array($this->shipments))
            return false;

        if (!count($this->shipments))
            return false;

        foreach ($this->items as $item) {
            if (!$item->is_shipped())
                return true;
        }

        return false;
    }

    public function is_fully_shipped(): bool {
        if (!is_array($this->shipments))
            return false;

        if (!count($this->shipments))
            return false;

        foreach ($this->items as $item) {
            if (!$item->is_shipped())
                return false;
        }

        return true;
    }

    public function has_attachments(): bool {
        return isset($this->media) && isset($this->media->attachments) && $this->media->attachments->itemsFound > 0;
    }

    public function get_attachments(): array {
        $attachments = [];

        if ($this->has_attachments()) {
            foreach ($this->media->attachments->items as $item) {
                $attachments[] = $item;
            }
        }

        return $attachments;
    }
}
