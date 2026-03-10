<?php

namespace Propeller\Includes\Model;

if ( ! defined( 'ABSPATH' ) ) exit;

use stdClass;

class BaseModel
{
    public function __construct() {}

    public function parse_arguments($arguments)
    {
        $args = [];

        if (is_array($arguments)) {
            foreach ($arguments as $key => $vals) {
                if (gettype($vals) == 'object') {
                    $val = str_replace("\'", "'", $vals->__toString());

                    $args[] = "$key: " . $val;
                } else if (gettype($vals) == 'string') {
                    $val = str_replace("\'", "'", $vals);

                    $args[] = "$key: \"$vals\"";
                } else if (gettype($vals) == 'boolean') {
                    $val = $vals ? "true" : "false";

                    $args[] = "$key: $val";
                } else {
                    $args[] = "$key: $vals";
                }
            }

            return implode(', ', $args);
        } else {
            return $arguments->__toString();
        }
    }

    public function extract_query($query)
    {
        preg_match('/^query {(.*)}/ms', $query, $matches);

        if (count($matches) > 1)
            return $matches[1];

        return '';
    }

    public static function attributes_lang_fragment()
    {
        $gql = '
            fragment WPAttributesFragment on AttributeResultResponse {
                items {
                    attribute {
                        id
                    }
                    attributeDescription {
                        id
                        name
                        units {
                            language
                            value
                        }
                        descriptions {
                            language
                            value
                        }
                        type
                    }
                    value {
                        __typename
                        ... on AttributeColorValue {
                            colorValue
                        }
                        ... on AttributeDecimalValue {
                            decimalValue
                        }
                        ... on AttributeDateTimeValue {
                            dateTimeValue
                        }
                        ... on AttributeEnumValue {
                            enumValues
                        }
                        ... on AttributeIntValue {
                            intValue
                        }
                        ... on AttributeTextValue {
                            textValues {
                                language
                                values
                            }
                        }
                    }
                }
                itemsFound
                offset
                page
                pages
                start
                end
            }
        ';

        $return = new stdClass();
        $return->query = $gql;

        return $return;
    }

    public static function attributes_fragment()
    {
        $gql = '
            fragment WPAttributesFragment on AttributeResultResponse {
                items {
                    attribute {
                        id
                    }
                    attributeDescription {
                        id
                        name
                        units {
                            language
                            value
                        }
                        descriptions {
                            language
                            value
                        }
                        type
                    }
                    value {
                        __typename
                        ... on AttributeColorValue {
                        colorValue
                        }
                        ... on AttributeDecimalValue {
                        decimalValue
                        }
                        ... on AttributeDateTimeValue {
                        dateTimeValue
                        }
                        ... on AttributeEnumValue {
                        enumValues
                        }
                        ... on AttributeIntValue {
                        intValue
                        }
                        ... on AttributeTextValue {
                            textValues {
                                language
                                values
                            }
                        }
                    }
                }
                itemsFound
                offset
                page
                pages
                start
                end
            }
        ';

        $return = new stdClass();
        $return->query = $gql;

        return $return;
    }

    public static function attributes()
    {
        $gql = '
            attributes(input: $attributes_filter) {
                ... WPAttributesFragment
            }
        ';

        $return = new stdClass();
        $return->query = $gql;

        return $return;
    }

    public static function alias_attributes($alias)
    {
        $gql = "
            $alias: attributes(input: \$alias_attributes_filter) {
                ... WPAttributesFragment
            }
        ";

        $return = new stdClass();
        $return->query = $gql;

        return $return;
    }

    public static function product_track_attributes()
    {
        $gql = new stdClass();
        $gql->query = "";
        $gql->variables = [];

        if (defined('PROPELLER_PRODUCT_TRACK_ATTR') && !empty(PROPELLER_PRODUCT_TRACK_ATTR)) {
            $attr_track = explode(',', PROPELLER_PRODUCT_TRACK_ATTR);
            // $offset = count($attr_track);

            $gql->variables = [
                "attributeDescription" => [
                    "names" => $attr_track,
                ]
            ];

            $gql->query = '
                trackAttributes: attributes(input: $product_track_attrs_filter) {
                    ... WPAttributesFragment
                }
            ';
        }

        return $gql;
    }

    public static function category_track_attributes()
    {
        $gql = new stdClass();
        $gql->query = "";
        $gql->variables = [];

        if (defined('PROPELLER_CATEGORY_TRACK_ATTR') && !empty(PROPELLER_CATEGORY_TRACK_ATTR)) {
            $attr_track = explode(',', PROPELLER_CATEGORY_TRACK_ATTR);
            // $offset = count($attr_track);

            $gql->variables = [
                "attributeDescription" => [
                    "names" => $attr_track,
                ]
            ];

            $gql->query = '
                trackAttributes: attributes(input: $category_track_attrs_filter) {
                    ... WPAttributesFragment
                }
            ';
        }

        return $gql;
    }

    public static function user_track_attributes()
    {
        $gql = new stdClass();
        $gql->query = "";
        $gql->variables = [];

        if (defined('PROPELLER_USER_TRACK_ATTR') && !empty(PROPELLER_USER_TRACK_ATTR)) {
            $attr_track = explode(',', PROPELLER_USER_TRACK_ATTR);
            $offset = count($attr_track);

            $gql->variables = [
                "attributeDescription" => [
                    "names" => $attr_track
                ],
                "offset" => $offset,
                "page" => 1
            ];

            $gql->query =  '
                trackAttributes: attributes(input: $user_track_attrs_filter) {
                    ... WPAttributesFragment
                }
            ';
        }

        return $gql;
    }

    public static function company_track_attributes()
    {
        $gql = new stdClass();
        $gql->query = "";
        $gql->variables = [];

        if (defined('PROPELLER_COMPANY_TRACK_ATTR') && !empty(PROPELLER_COMPANY_TRACK_ATTR)) {
            $attr_track = explode(',', PROPELLER_COMPANY_TRACK_ATTR);
            $offset = count($attr_track);

            $gql->variables = [
                "attributeDescription" => [
                    "names" => $attr_track
                ],
                "offset" => $offset,
                "page" => 1
            ];

            $gql->query =  '
                trackAttributes: attributes(input: $company_track_attrs_filter) {
                    ... WPAttributesFragment
                }
            ';
        }

        return $gql;
    }

    public function build_products_args($provided_args)
    {
        $args_types = [
            "manufacturers" => '[String!]',
            "supplier" => '[String!]',
            "brand" => '[String!]',
            "categoryId" => 'Int',
            "class" => 'ProductClass',
            "tag" => '[String!]',
            "page" => 'Int! = 1',
            "offset" => 'Int! = 12',
            "language" => 'String!',
            "attribute" => '[TextFilterInput!]',
            "status" => '[ProductStatus!]',
            "hidden" => 'Boolean',
            "sort" => '[SortInput!]',
            "id" => '[Int!]',
            "term" => 'String'
        ];

        $collected_args = [];
        $collected_params = [];
        foreach (array_keys($provided_args) as $key) {
            $collected_args[] = "\$$key: " . $args_types[$key];
            $collected_params[] = "$key: \$$key";
        }

        $return = new stdClass();

        $return->args = $collected_args;
        $return->params = $collected_params;

        return $return;
    }
}
