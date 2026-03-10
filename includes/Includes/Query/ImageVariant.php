<?php

namespace Propeller\Includes\Query;

if ( ! defined( 'ABSPATH' ) ) exit;

class ImageVariant {
    static function setTransformationOptions($args) {  
        $name = $args['name'];
        unset($args['name']);

        $params = [
            "name" => $name,
            "transformation" => $args
        ];
           
        return $params;
    }

    static function setTransformations($transformations) {
        $output = [];

        if (is_array($transformations) && count($transformations)) {
            $output = [
                "transformations" => $transformations
            ];
        }

        return $output;
    }
}