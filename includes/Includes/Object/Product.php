<?php
declare(strict_types=1);

namespace Propeller\Includes\Object;

if ( ! defined( 'ABSPATH' ) ) exit;

use stdClass;

/**
 * @property string $class Product/Cluster class identifier from the API
 */
class Product extends BaseObject
{
    const PRICE_FROM = 'FROM';                          // Price from [Cheapest price].
    const PRICE_FROM_FOR = 'FROM_FOR';                  // Old price [Suggested price] new price [Sale price]
    const LISTPRICE_OURPRICE = 'LISTPRICE_OURPRICE';    // List price [Suggested price] our price [Sale price]
    const PACKAGE = 'PACKAGE';                          // Price [Sale price/Order unit] per [Order UOM].
    const PER_UOM = 'PER_UOM';                          // Price from [Cheapest price/Package unit] per [Package UOM]
    const PRICE_ON_REQUEST = 'ON_REQUEST';              // Price on request
    const PRICE_DEFAULT = 'DEFAULT';                    // Too blue.

    public ?stdClass $attributes = null;
    public ?stdClass $trackAttributes = null;
    public ?stdClass $attributeItems = null;
    public ?stdClass $trackAttributeItems = null;
    public array $images = [];
    public array $videos = [];
    public array $documents = [];
    public mixed $category = null;

    public mixed $productId = null;
    public ?stdClass $price = null;
    public mixed $status = null;
    public ?string $sku = null;
    public ?string $manufacturer = null;
    public mixed $minimumQuantity = null;
    public ?array $name = null;
    public ?array $slug = null;
    public ?array $categoryPath = null;
    protected ?stdClass $media = null;
    public ?stdClass $priceData = null;

    public function __construct(object $product)
    {
        parent::__construct($product);

        $this->trackAttributeItems = new stdClass();
        $this->trackAttributeItems->itemsFound = 0;

        $this->attributeItems = new stdClass();
        $this->attributeItems->itemsFound = 0;

        if (isset($product->trackAttributes) && isset($product->trackAttributes->itemsFound) && $product->trackAttributes->itemsFound > 0) {
            $attrs = new AttributeArray($product->trackAttributes);
            $this->trackAttributes = $attrs->get_non_empty_attrs();

            $this->trackAttributeItems->itemsFound = $product->trackAttributes->itemsFound;
            $this->trackAttributeItems->offset = $product->trackAttributes->offset;
            $this->trackAttributeItems->page = $product->trackAttributes->page;
            $this->trackAttributeItems->pages = $product->trackAttributes->pages;
            $this->trackAttributeItems->start = $product->trackAttributes->start;
            $this->trackAttributeItems->end = $product->trackAttributes->end;
        }

        if (isset($product->attributes) && !is_array($product->attributes) && $product->attributes->itemsFound > 0) {
            $attrs = new AttributeArray($product->attributes);

            $this->attributes = $attrs->get_non_empty_attrs();

            $this->attributeItems->itemsFound = $product->attributes->itemsFound;
            $this->attributeItems->offset = $product->attributes->offset;
            $this->attributeItems->page = $product->attributes->page;
            $this->attributeItems->pages = $product->attributes->pages;
            $this->attributeItems->start = $product->attributes->start;
            $this->attributeItems->end = $product->attributes->end;
        }

        if ($this->has_images())
            $this->get_image_variants();

        if ($this->has_videos())
            $this->get_videos();

        if ($this->has_documents())
            $this->get_documents();

        if (!isset($this->category) || !$this->category) {
            if (isset($this->categoryPath) && is_array($this->categoryPath) && count($this->categoryPath))
                $this->category = $this->categoryPath[count($this->categoryPath) - 1];
        }
    }

    public function has_images(): bool
    {
        return isset($this->media) && isset($this->media->images) && $this->media->images->itemsFound > 0;
    }

    public function has_videos(): bool
    {
        return isset($this->media) && isset($this->media->videos) && $this->media->videos->itemsFound > 0;
    }

    public function has_documents(): bool
    {
        return isset($this->media) && isset($this->media->documents) && $this->media->documents->itemsFound > 0;
    }

    public function get_image_variants(): void
    {
        if ($this->has_images()) {
            foreach ($this->media->images->items as $image) {
                $img = new stdClass();

                $img->alt = $image->alt;
                $img->description = $image->description;
                $img->tags = $image->tags;
                $img->type = $image->type;
                $img->createdAt = $image->createdAt;
                $img->priority = $image->priority;

                if (isset($image->imageVariants) && count($image->imageVariants)) {
                    $img->images = $image->imageVariants;

                    $this->images[] = $img;
                }
            }
        }
    }

    public function get_videos(): void
    {
        if ($this->has_videos()) {
            foreach ($this->media->videos->items as $video) {
                $vid = new stdClass();

                $vid->alt = $video->alt;
                $vid->description = $video->description;
                $vid->tags = $video->tags;
                $vid->type = $video->type;
                $vid->createdAt = $video->createdAt;
                $vid->priority = $video->priority;

                if (isset($video->videos) && count($video->videos)) {
                    $vid->videos = $video->videos;

                    $this->videos[] = $vid;
                }
            }
        }
    }

    public function get_documents(): void
    {
        if ($this->has_documents()) {
            foreach ($this->media->documents->items as $document) {
                $doc = new stdClass();

                $doc->alt = $document->alt;
                $doc->description = $document->description;
                $doc->tags = $document->tags;
                $doc->type = $document->type;
                $doc->createdAt = $document->createdAt;
                $doc->priority = $document->priority;

                if (isset($document->documents) && count($document->documents)) {
                    $doc->documents = $document->documents;

                    $this->documents[] = $doc;
                }
            }
        }
    }

    public function has_attributes(): bool
    {
        return $this->attributeItems->itemsFound > 0;
    }

    public function get_attributes(): array
    {
        return $this->attributes->items;
    }

    public function get_name(): string
    {
        if (isset($this->name) && is_array($this->name) && count($this->name)) {
            $lang = PROPELLER_LANG;

            // If only one name is available, return it
            if (count($this->name) == 1 && !empty($this->name[0]->value))
                return $this->name[0]->value;

            $found = array_filter($this->name, function ($obj) use ($lang) {
                return strtolower($obj->language) == strtolower($lang) &&
                    !empty($obj->value);
            });

            if (!count($found))
                $found = array_filter($this->name, function ($obj) {
                    return strtolower($obj->language) == strtolower(PROPELLER_FALLBACK_LANG) &&
                        !empty($obj->value);
                });

            if (count($found)) {
                $item = current($found);
                return $item !== false ? (string)$item->value : '';
            }

            return '';
        }

        return '';
    }

    public function get_slug(): ?string
    {
        if (isset($this->slug) && is_array($this->slug) && count($this->slug)) {
            $lang = PROPELLER_LANG;

            // If only one slug is available, return it
            if (count($this->slug) == 1 && !empty($this->slug[0]->value))
                return $this->slug[0]->value;

            $found = array_filter($this->slug, function ($obj) use ($lang) {
                return strtolower($obj->language) == strtolower($lang) &&
                    !empty($obj->value);
            });

            if (!count($found))
                $found = array_filter($this->slug, function ($obj) {
                    return strtolower($obj->language) == strtolower(PROPELLER_FALLBACK_LANG) &&
                        !empty($obj->value);
                });

            if (count($found)) {
                $item = current($found);
                return $item !== false ? (string)$item->value : null;
            }

            return null;
        }

        return null;
    }

    public function has_track_attributes(): bool
    {
        return is_array($this->trackAttributes->items) && count($this->trackAttributes->items) > 0;
    }

    public function get_track_attr_by_name(string $name): ?Attribute
    {
        if (!is_array($this->trackAttributes->items))
            return null;

        $found = array_filter($this->trackAttributes->items, function ($obj) use ($name) {
            return $obj->attributeDescription->name == $name;
        });

        if (count($found))
            return current($found);

        return null;
    }

    public function get_track_attr_value(string $name): mixed
    {
        if (!is_array($this->trackAttributes->items))
            return null;

        $found = array_filter($this->trackAttributes->items, function ($obj) use ($name) {
            return $obj->attributeDescription->name == $name;
        });

        if (count($found)) {
            $item = current($found);
            return $item !== false ? $item->get_value() : null;
        }

        return null;
    }

    public function get_attr_by_name(string $name): ?Attribute
    {
        if (!is_array($this->attributes->items))
            return null;

        $found = array_filter($this->attributes->items, function ($obj) use ($name) {
            return $obj->attributeDescription->name == $name;
        });

        if (count($found))
            return current($found);

        return null;
    }

    public function get_attr_value(string $name): mixed
    {
        if (!is_array($this->attributes->items))
            return null;

        $found = array_filter($this->attributes->items, function ($obj) use ($name) {
            return $obj->attributeDescription->name == $name;
        });

        if (count($found)) {
            $item = current($found);
            return $item !== false ? $item->get_value() : null;
        }

        return null;
    }

    public function is_price_on_request(): bool
    {
        return isset($this->priceData) && isset($this->priceData->display) && $this->priceData->display == self::PRICE_ON_REQUEST;
    }
}
