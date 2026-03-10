<?php

namespace Propeller\Includes\Controller;

if ( ! defined( 'ABSPATH' ) ) exit;

use stdClass;

class ValuesetController extends BaseController {
    const VALUESET_HIDE_EXTRA = 'HIDE';

    protected $model;

    public function __construct() {
        parent::__construct();

        $this->model = $this->load_model('valueset');
    }

    public function get_valuesets($args) {
        $type = "valuesets";

        $gql = $this->model->get_valuesets($args);

        return $this->query($gql, $type);
    }

    public function get_valueset($args) {
        $type = "valuesets";

        $gql = $this->model->get_valuesets($args);

        $response = $this->query($gql, $type);

        if (is_object($response) && $response->itemsFound > 0)
            return $this->parse_valueset($response->items[0]);

        // return null;
        return $response;
    }

    private function parse_valueset($result) {
        $valueset = new stdClass();

        if (!defined('PROPELLER_LANG'))
            set_propel_locale();

        $lang = PROPELLER_LANG;

        $desc_found = array_filter($result->descriptions, function($v) use ($lang) {
            return $v->language == $lang;
        });

        if (count($desc_found)) {
            if (!defined('PROPELLER_ICP_COUNTRY'))
                \Propeller\Propeller::register_behavior();

            $fallback_lang = PROPELLER_ICP_COUNTRY;

            $desc_found = array_filter($result->descriptions, function($v) use ($fallback_lang) {
                return $v->language == $fallback_lang;
            });
        }

        $valueset->description = current($desc_found)->value;

        $valueset->items = [];

        foreach ($result->valuesetItems->items as $v_item) {
            // if ($v_item->extra == self::VALUESET_HIDE_EXTRA)
            //     continue;

            $valset_item = new stdClass();
            
            $valset_item->value = $v_item->value;
            $valset_item->hide = $v_item->extra == self::VALUESET_HIDE_EXTRA;

            $lang = PROPELLER_LANG;

            $val_found = array_filter($v_item->descriptions, function($i) use ($lang) {
                return $i->language == $lang;
            });

            if (count($val_found)) {
                $fallback_lang = PROPELLER_ICP_COUNTRY;

                $val_found = array_filter($v_item->descriptions, function($i) use ($fallback_lang) {
                    return $i->language == $fallback_lang;
                });
            }

            $valset_item->description = current($val_found)->value;

            $valueset->items[] = $valset_item;
        }

        return $valueset;
    }
}