<?php

namespace Propeller\Includes\Query;

if ( ! defined( 'ABSPATH' ) ) exit;

use Propeller\Includes\Enum\MediaType;

class Media {
    public static $query;

    static function get($params, $type, $is_spare_part = false) {
        $media_query = null;

        switch ($type) {
            case MediaType::IMAGES: 
                $media_query = !$is_spare_part 
                    ? MediaImages::get_media_images_query($params)
                    : MediaImages::get_spare_parts_media_images_query($params);

                break;
            case MediaType::VIDEOS: 
                $media_query = !$is_spare_part 
                    ? MediaVideos::get_media_videos_query($params)
                    : MediaVideos::get_spare_parts_media_videos_query($params);
                
                break;
            case MediaType::DOCUMENTS: 
                $media_query = !$is_spare_part 
                    ? MediaDocuments::get_media_documents_query($params)
                    : MediaDocuments::get_spare_parts_media_documents_query($params);

                break;
        }

        return $media_query;
    }
}