<?php

namespace Propeller\Includes\Query;

if ( ! defined( 'ABSPATH' ) ) exit;

use GraphQL\Query;
use Propeller\Includes\Enum\MediaSort;
use stdClass;

class MediaVideos {
    static function get_media_videos_query($args) {
        $search_params = [
            'sort' => (isset($args['sort']) ? sanitize_text_field($args['sort']) : MediaSort::ASC),
            'page' => (isset($args['page']) ? (int) $args['page'] : 1),
            'offset' => (isset($args['offset']) ? (int) $args['offset'] : 12)
        ];

        $search_args = MediaVideo::setSearchOptions($search_params);
        
        $gql = '
            fragment WPProductVideoFragment on ProductMedia {
                videos(search: $vid_search) {
                    itemsFound
                    offset
                    page
                    pages
                    start
                    end
                    items {
                        alt (language: $language) {
                            value
                            language
                        }
                        description (language: $language) {
                            value
                            language
                        }
                        tags (language: $language) {
                            values
                            language
                        }
                        type
                        createdAt
                        priority
                        videos {
                            language
                            uri
                            mimeType
                        }
                    }
                }
            }
        ';

        $variables = [
            "vid_search" => $search_args
        ];

        $return = new stdClass();
        $return->query = $gql;
        $return->variables = $variables;

        return $return;
    }

    static function get_spare_parts_media_videos_query($args) {
        $search_params = [
            'sort' => (isset($args['sort']) ? sanitize_text_field($args['sort']) : MediaSort::ASC),
            'page' => (isset($args['page']) ? (int) $args['page'] : 1),
            'offset' => (isset($args['offset']) ? (int) $args['offset'] : 12)
        ];

        $search_args = MediaVideo::setSearchOptions($search_params);
        
        $gql = '
            fragment WPSparePartVideoFragment on SparePartsMachineMedia {
                videos(search: $vid_search) {
                    itemsFound
                    offset
                    page
                    pages
                    start
                    end
                    items {
                        alt (language: $language) {
                            value
                            language
                        }
                        description (language: $language) {
                            value
                            language
                        }
                        tags (language: $language) {
                            values
                            language
                        }
                        type
                        createdAt
                        priority
                        videos {
                            language
                            uri
                            mimeType
                        }
                    }
                }
            }
        ';

        $variables = [
            "vid_search" => $search_args
        ];

        $return = new stdClass();
        $return->query = $gql;
        $return->variables = $variables;

        return $return;
    }
}