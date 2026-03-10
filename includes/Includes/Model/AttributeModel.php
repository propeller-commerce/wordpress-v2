<?php

namespace Propeller\Includes\Model;

if ( ! defined( 'ABSPATH' ) ) exit;

use stdClass;

class AttributeModel extends BaseModel {
    public function __construct() {
        parent::__construct();
    }

    public function create_description($attr_description_args) {
        $gql = '
            mutation WPAttrDescriptionCreateMutation(
                $attr_description_args: AttributeDescriptionCreateInput!
            ){
                attributeDescriptionCreate(input: $attr_description_args) {
                    ... WPAttrDescriptionFragment
                }
            }
        ';

        $queries = [
            self::attr_description_fragment()->query,
            $gql
        ];

        $return = new stdClass();
        $return->query = implode("\n\n", $queries);
        $return->variables = [
            'attr_description_args' => $attr_description_args
        ];

        return $return;
    }

    public function get_description($attr_description_args) {
        $gql = '
            query WPAttrDescriptionsQuery(
                $attr_description_args: AttributeDescriptionSearchInput!
            ){
                attributeDescriptions(input: $attr_description_args) {
                    items {
                        ... WPAttrDescriptionFragment
                    }
                    itemsFound
                    offset
                    page
                    pages
                    start
                    end
                }
            }
        ';

        $queries = [
            self::attr_description_fragment()->query,
            $gql
        ];

        $return = new stdClass();
        $return->query = implode("\n\n", $queries);
        $return->variables = [
            'attr_description_args' => $attr_description_args
        ];

        return $return;
    }

    public function create_attribute($attr_create_args) {
        $gql = '
            mutation WPAttrCreateMutation(
                $attr_create_args: AttributeCreateInput!
            ){
                attributeCreate(input: $attr_create_args) {
                    id
                    value {
                        id
                    }
                    attributeDescription {
                        ... WPAttrDescriptionFragment
                    }
                }
            }
        ';

        $queries = [
            self::attr_description_fragment()->query,
            $gql
        ];

        $return = new stdClass();
        $return->query = implode("\n\n", $queries);
        $return->variables = [
            'attr_create_args' => $attr_create_args
        ];

        return $return;
    }

    public function update_attribute($attr_id, $attr_update_args) {
        $gql = '
            mutation WPAttrCreateMutation(
                $attr_id: String!
                $attr_update_args: AttributeUpdateInput!
            ){
                attributeUpdate(id: $attr_id input: $attr_update_args) {
                    id
                    value {
                        id
                    }
                    attributeDescription {
                        ... WPAttrDescriptionFragment
                    }
                }
            }
        ';

        $queries = [
            self::attr_description_fragment()->query,
            $gql
        ];

        $return = new stdClass();
        $return->query = implode("\n\n", $queries);
        $return->variables = [
            'attr_id' => $attr_id,
            'attr_update_args' => $attr_update_args
        ];

        return $return;
    }

    public static function attr_description_fragment() {
        $gql = '
            fragment WPAttrDescriptionFragment on AttributeDescription {
                id
                name
                descriptions {
                    language
                    value
                }
                units {
                    language
                    value
                }
                attributeClass
                type
                valuesetId
                group
                isSearchable
                isPublic
                isSystem
                isHidden
                defaultValue {
                    id
                }
            }
        ';

        $return = new stdClass();
        $return->query = $gql;

        return $return;
    }
}