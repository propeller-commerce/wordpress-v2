<?php

namespace Propeller\Includes\Query;

if ( ! defined( 'ABSPATH' ) ) exit;

use Propeller\Includes\Enum\MediaSort;
use stdClass;

class MediaDocuments {
    static function get_media_documents_query($args) {
        $search_params = [
            'sort' => (isset($args['sort']) ? sanitize_text_field($args['sort']) : MediaSort::ASC),
            'page' => (isset($args['page']) ? (int) $args['page'] : 1),
            'offset' => (isset($args['offset']) ?(int)  $args['offset'] : 12)
        ];

        $search_args = MediaDocument::setSearchOptions($search_params);

        $gql = '
            fragment WPProductDocumentFragment on ProductMedia {
                documents(search: $doc_search) {
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
                        documents {
                            language
                            originalUrl
                            mimeType
                        }
                    }
                }
            }
        ';

        $variables = [
            "doc_search" => $search_args
        ];

        $return = new stdClass();
        $return->query = $gql;
        $return->variables = $variables;

        return $return;
    }

    static function get_spare_parts_media_documents_query($args) {
        $search_params = [
            'sort' => (isset($args['sort']) ? sanitize_text_field($args['sort']) : MediaSort::ASC),
            'page' => (isset($args['page']) ? (int) $args['page'] : 1),
            'offset' => (isset($args['offset']) ?(int)  $args['offset'] : 12)
        ];

        $search_args = MediaDocument::setSearchOptions($search_params);

        $gql = '
            fragment WPSparePartDocumentFragment on SparePartsMachineMedia {
                documents(search: $parts_doc_search) {
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
                        documents {
                            language
                            originalUrl
                            mimeType
                        }
                    }
                }
            }
        ';

        $variables = [
            "parts_doc_search" => $search_args
        ];

        $return = new stdClass();
        $return->query = $gql;
        $return->variables = $variables;

        return $return;
    }
}