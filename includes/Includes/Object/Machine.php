<?php
declare(strict_types=1);

namespace Propeller\Includes\Object;

if ( ! defined( 'ABSPATH' ) ) exit;

use stdClass;

class Machine extends BaseObject {
    public array $images = [];
    public array $videos = [];
    public array $documents = [];
    protected ?stdClass $media = null;

    public function __construct(object $machine) {
        parent::__construct($machine);

        if ($this->has_images())
            $this->get_image_variants();

        if ($this->has_videos())
            $this->get_videos();

        if ($this->has_documents())
            $this->get_documents();
    }

    public function has_images(): bool {
        return isset($this->media) && isset($this->media->images) && $this->media->images->itemsFound > 0;
    }

    public function has_videos(): bool {
        return isset($this->media) && isset($this->media->videos) && $this->media->videos->itemsFound > 0;
    }

    public function has_documents(): bool {
        return isset($this->media) && isset($this->media->documents) && $this->media->documents->itemsFound > 0;
    }

    public function get_image_variants(): void {
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

    public function get_videos(): void {
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

    public function get_documents(): void {
        if ($this->has_documents()) {
            foreach ($this->media->documents->items as $document) {
                $doc = new stdClass();

                $doc->alt = count($document->alt) ? $document->alt[0]->value : '';
                $doc->description = count($document->description) ? $document->description[0]->value : '';
                $doc->tags = count($document->tags) && count($document->tags[0]->values) ? implode(' ', $document->tags[0]->values) : '';
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
}
