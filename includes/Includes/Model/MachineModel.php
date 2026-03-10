<?php

namespace Propeller\Includes\Model;

if ( ! defined( 'ABSPATH' ) ) exit;

use Propeller\Includes\Controller\UserController;
use Propeller\Includes\Model\ProductModel;
use stdClass;

class MachineModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();
    }

    public function installations($machine_queries, $parts_img_args, $parts_docs_args, $params)
    {
        $concat = implode("\n", $machine_queries);

        $params_keys = $this->format_installation_params(array_keys($params));

        $arguments = implode("\n", $params_keys);

        $gql = "
            query WPSparePartsQuery(
                $arguments
            ){
               $concat 
            }
        ";

        $queries = [
            self::machine_fragment()->query,
            $parts_img_args->query,
            $parts_docs_args->query,
            $gql
        ];

        $variables = array_merge(
            $parts_img_args->variables,
            $params
        );

        $return = new stdClass();
        $return->query = implode("\n\n", $queries);
        $return->variables = $variables;

        return $return;
    }

    public function get_machines($arguments, $qry_params, $images_args, $documents_args, $machine_images_args, $machine_documents_args, $language)
    {
        $parts_language = $language;
        $language = "EN";
        $taxZone = PROPELLER_DEFAULT_TAXZONE;

        $product_track_attrs_filter = self::product_track_attributes();

        $gql = "
            query WPMachineQuery (
                \$slug: String
                \$language: String
                \$machines_language: String
                \$machines_input: SparePartsMachineProductSearchInput
                \$price_input: PriceCalculateProductInput
                \$product_track_attrs_filter: AttributeResultSearchInput!
                \$img_search: MediaImageProductSearchInput
                \$img_transform: TransformationsInput!
                \$parts_img_search: ObjectMediaSearchInput
                \$parts_doc_search: ObjectMediaSearchInput
            ) {
                machine (slug: \$slug language: \$language) {
                    ... WPMachineNoMediaFragment
                    machines {
                        ... WPMachineFragment
                    }
                    sparePartProducts (input: \$machines_input) {
                        ... WPSparePartProductsFragment
                    }
                }
            }        
        ";

        $queries = [
            self::machine_fragment()->query,
            self::machine_no_media_fragment()->query,
            self::spare_parts_product_fragment()->query,
            defined('PROPELLER_USE_LANGUAGE_IN_ATTRIBUTES') && PROPELLER_USE_LANGUAGE_IN_ATTRIBUTES
                ? self::attributes_lang_fragment()->query
                : self::attributes_fragment()->query,
            $product_track_attrs_filter->query,
            ProductModel::product_price_fragment()->query,
            ProductModel::product_surcharge_fragment()->query,
            ProductModel::product_inventory_fragment()->query,
            $machine_images_args->query,
            $machine_documents_args->query,
            $images_args->query,
            $gql
        ];

        $variables = array_merge(
            $arguments,
            [
                'language' => $language,
                'machines_language' => $parts_language,
                'machines_input' => $qry_params,
                'price_input' => ProductModel::price_input_array($taxZone),
                'product_track_attrs_filter' => count($product_track_attrs_filter->variables) ? $product_track_attrs_filter->variables : new stdClass(),
                'filters_input' => [
                    'isSearchable' => true
                ]
            ],
            $images_args->variables,
            $machine_images_args->variables,
            $machine_documents_args->variables,
            self::spare_parts_product_fragment()->variables
        );

        $return = new stdClass();
        $return->query = implode("\n\n", $queries);
        $return->variables = $variables;

        return $return;
    }

    public function get_machine_query($index)
    {
        $gql = "
            machine_$index : machine(source: \$source sourceId: \$sourceId_$index language: \$language) {
                ... WPMachineFragment
                machines {
                    ... WPMachineFragment
                }
            }
        ";

        return $gql;
    }

    public static function machine_fragment()
    {
        $gql = '
            fragment WPMachineFragment on SparePartsMachine {
                id
                name (language: $language) {
                    language
                    value
                }
                description (language: $language) {
                    language
                    value
                }
                slug (language: $language) {
                    language
                    value
                }
                slugs: slug {
                    language
                    value
                }
                media {
                    ... WPSparePartImageFragment
                    ... WPSparePartDocumentFragment
                }
            }
        ';

        $return = new stdClass();
        $return->query = $gql;

        return $return;
    }

    public static function machine_no_media_fragment()
    {
        $gql = '
            fragment WPMachineNoMediaFragment on SparePartsMachine {
                id
                name (language: $language) {
                    language
                    value
                }
                description (language: $language) {
                    language
                    value
                }
                slug (language: $language) {
                    language
                    value
                }
                slugs: slug {
                    language
                    value
                }
            }
        ';

        $return = new stdClass();
        $return->query = $gql;

        return $return;
    }

    public static function spare_part_fragment($media_images_gql)
    {
        $product_track_attributes = self::product_track_attributes();

        $gql = '
            fragment WPSparePartFragment on SparePart {
                id
                sku
                quantity
                name (language: $language) {
                    language
                    value
                } 
                product {
                    ... WPGridProductFragment
                }
            }
        ';

        $queries = [
            ProductModel::grid_product_fragment()->query,
            $product_track_attributes->query,
            $gql
        ];

        $return = new stdClass();
        $return->query = implode("\n\n", $queries);
        $return->variables = [];

        return $return;
    }

    public function spare_parts_product_fragment()
    {
        $gql = '
            fragment WPSparePartProductsFragment on SparePartsResponse {
                itemsFound
                offset
                page
                pages
                start
                end
                minPrice
                maxPrice
                items {
                    id
                    sku
                    quantity
                    name (language: $machines_language) {
                        language
                        value
                    }
                    product {
                        class   
                        slug: slugs (language: $machines_language) {
                            value
                            language
                        } 
                        name: names(language: $machines_language) {
                            value
                            language
                        }
                        sku                   
                        ... on Product {
                            ... WPGridProductFragment
                        }
                        ... on Cluster {
                            clusterId
                            urlId: clusterId
                            defaultProduct {
                                ... WPGridProductFragment
                            }
                        }
                    }
                }
                filters {
                    ... WPProductFiltersFragment
                }
            }            
        ';

        $queries = [
            ProductModel::grid_product_fragment(false)->query,
            ProductModel::products_filters_fragment()->query,
            $gql
        ];

        $return = new stdClass();
        $return->query = implode("\n\n", $queries);
        $return->variables = [];

        return $return;
    }

    private function format_installation_params($keys)
    {
        $types = [
            'img_search' => 'MediaImageProductSearchInput',
            'img_transform' => 'TransformationsInput!',
            'doc_search' => 'MediaDocumentProductSearchInput',
            'product_track_attrs_filter' => 'AttributeResultSearchInput!',
            'parts_img_search' => 'ObjectMediaSearchInput',
            'parts_doc_search' => 'ObjectMediaSearchInput',
            'price_input' => 'PriceCalculateProductInput',
            'fav_list_input' => 'FavoriteListsSearchInput',
        ];

        $params = [];

        foreach ($keys as $key)
            $params[] = "\$$key: " . (array_key_exists($key, $types) ? $types[$key] : "String");

        return $params;
    }
}
