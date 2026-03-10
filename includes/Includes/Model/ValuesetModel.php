<?php

namespace Propeller\Includes\Model;

if ( ! defined( 'ABSPATH' ) ) exit;

use stdClass;

class ValuesetModel extends BaseModel {
    public function __construct() {
        
    }

    public function get_valuesets($valuesets_args) {
        $gql = '
            query WPValuesetsQuery (
                $valsets_input: ValuesetSearchInput
            ){
                valuesets (input: $valsets_input) {
                    itemsFound
                    pages
                    items {
                        id
                        name
                        type
                        descriptions {
                            language
                            value
                        }
                        valuesetItems (input: {
                            offset: 1000
                        }) {                        
                            itemsFound
                            items {
                                value
                                descriptions {
                                    language
                                    value
                                }
                                extra
                            }
                        }
                    }
                }
            }
        ';

        $return = new stdClass();
        $return->query = $gql;
        $return->variables = [
            'valsets_input' => $valuesets_args
        ];

        return $return;
    }
}
