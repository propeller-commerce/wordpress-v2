<?php

namespace Propeller\Includes\Query;

if ( ! defined( 'ABSPATH' ) ) exit;

use Propeller\Includes\Enum\ImageFit;
use Propeller\Includes\Enum\ImageFormat;
use Propeller\Includes\Enum\MediaImagesType;
use Propeller\Includes\Enum\MediaSort;
use stdClass;

class MediaImages {
    static function get_media_images_query($args) {
        $search_params = [
            'sort' => (isset($args['sort']) ? sanitize_text_field( $args['sort']) : MediaSort::ASC),
            'page' => (isset($args['page']) ? (int) $args['page'] : 1),
            'offset' => (isset($args['offset']) ? (int) $args['offset'] : 12)
        ];

        $width = PROPELLER_PRODUCT_IMG_MEDIUM_WIDTH;
        $height = PROPELLER_PRODUCT_IMG_MEDIUM_HEIGHT;

        if (isset($args['name'])) {
            switch ($args['name']) {
                case MediaImagesType::SMALL: 
                    $width = PROPELLER_PRODUCT_IMG_SMALL_WIDTH;
                    $height = PROPELLER_PRODUCT_IMG_SMALL_HEIGHT;

                    break;
                case MediaImagesType::MEDIUM: 
                    $width = PROPELLER_PRODUCT_IMG_MEDIUM_WIDTH;
                    $height = PROPELLER_PRODUCT_IMG_MEDIUM_HEIGHT;

                    break;
                case MediaImagesType::LARGE: 
                    $width = PROPELLER_PRODUCT_IMG_LARGE_WIDTH;
                    $height = PROPELLER_PRODUCT_IMG_LARGE_HEIGHT;

                    break;
                default: 
                    $width = PROPELLER_PRODUCT_IMG_MEDIUM_WIDTH;
                    $height = PROPELLER_PRODUCT_IMG_MEDIUM_HEIGHT;

                    break;
            }
        }

        if (isset($args['width'])) 
            $width = $args['width'];

        if (isset($args['height'])) 
            $height = $args['height'];

        $transformation_params = [
            'format' => (isset($args['format']) ? $args['format'] : ImageFormat::WEBP),
            'height' => $height,
            'width' => $width,
            'name' => (isset($args['name']) ? $args['name'] : MediaImagesType::MEDIUM),
            'fit' => ImageFit::BOUNDS
        ];

        $search_args = MediaImage::setSearchOptions($search_params);
        $transform_args = ImageVariant::setTransformations([ImageVariant::setTransformationOptions($transformation_params)]);

        $gql = '
            fragment WPProductImageFragment on ProductMedia {
                images(search: $img_search) {
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
                        imageVariants(input: $img_transform) {
                            name
                            language
                            url
                            mimeType
                        }
                    }
                }
            }
        ';

        $variables = [
            "img_search" => $search_args,
            "img_transform" => $transform_args,
        ];

        $return = new stdClass();
        $return->query = $gql;
        $return->variables = $variables;

        return $return;
    }

    static function get_spare_parts_media_images_query($args) {
        $search_params = [
            'sort' => (isset($args['sort']) ? sanitize_text_field( $args['sort']) : MediaSort::ASC),
            'page' => (isset($args['page']) ? (int) $args['page'] : 1),
            'offset' => (isset($args['offset']) ? (int) $args['offset'] : 12)
        ];

        $width = PROPELLER_PRODUCT_IMG_MEDIUM_WIDTH;
        $height = PROPELLER_PRODUCT_IMG_MEDIUM_HEIGHT;

        if (isset($args['name'])) {
            switch ($args['name']) {
                case MediaImagesType::SMALL: 
                    $width = PROPELLER_PRODUCT_IMG_SMALL_WIDTH;
                    $height = PROPELLER_PRODUCT_IMG_SMALL_HEIGHT;

                    break;
                case MediaImagesType::MEDIUM: 
                    $width = PROPELLER_PRODUCT_IMG_MEDIUM_WIDTH;
                    $height = PROPELLER_PRODUCT_IMG_MEDIUM_HEIGHT;

                    break;
                case MediaImagesType::LARGE: 
                    $width = PROPELLER_PRODUCT_IMG_LARGE_WIDTH;
                    $height = PROPELLER_PRODUCT_IMG_LARGE_HEIGHT;

                    break;
                default: 
                    $width = PROPELLER_PRODUCT_IMG_MEDIUM_WIDTH;
                    $height = PROPELLER_PRODUCT_IMG_MEDIUM_HEIGHT;

                    break;
            }
        }

        if (isset($args['width'])) 
            $width = $args['width'];

        if (isset($args['height'])) 
            $height = $args['height'];

        $transformation_params = [
            'format' => (isset($args['format']) ? $args['format'] : ImageFormat::WEBP),
            'height' => $height,
            'width' => $width,
            'name' => (isset($args['name']) ? $args['name'] : MediaImagesType::MEDIUM),
            'fit' => ImageFit::BOUNDS
        ];

        $search_args = MediaImage::setSearchOptions($search_params);
        $transform_args = ImageVariant::setTransformations([ImageVariant::setTransformationOptions($transformation_params)]);

        $gql = '
            fragment WPSparePartImageFragment on SparePartsMachineMedia {
                images(search: $parts_img_search) {
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
                        imageVariants(input: $img_transform) {
                            name
                            language
                            url
                            mimeType
                        }
                    }
                }
            }
        ';

        $variables = [
            "parts_img_search" => $search_args,
            "img_transform" => $transform_args,
        ];

        $return = new stdClass();
        $return->query = $gql;
        $return->variables = $variables;

        return $return;
    }
    
}