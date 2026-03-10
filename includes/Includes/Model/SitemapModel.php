<?php

namespace Propeller\Includes\Model;

if ( ! defined( 'ABSPATH' ) ) exit;

use stdClass;

class SitemapModel extends BaseModel {
    public function __construct() {
        
    }

    public function get_menu_structure($base_catalog_id, $language, $depth) {
        $category_gql = "
            categories {
                urlId: categoryId
                name(language: \$language) {
                    value
                    language
                }
                slug(language: \$language) {
                    value
                    language
                }
            ";

        $root_gql = "
            query WPSitemapMenuStructureQuery(
                \$base_catalog_id: Float
                \$language: String
            ){
                category(categoryId: \$base_catalog_id) {
        ";

        for ($i = 0; $i < $depth; $i++) {
            $root_gql .= $category_gql;
        }

        for ($i = 0; $i < $depth; $i++) {
            $root_gql .= " } ";
        }

        $root_gql .= "}
            }
        ";

        $return = new stdClass();
        $return->query = $root_gql;
        $return->variables = [
            'base_catalog_id' => $base_catalog_id,
            'language' => $language
        ];
        
        return $return;
    }

    public function get_products($language, $offset = 12, $page = 1) {
        $gql = '
            query WPSitemapProductsQuery(
                $page: Int! = 1
                $offset: Int! = 12
                $language: String
            ){
                products (input: { offset: $offset page: $page language: $language }) {
                    itemsFound
                    offset
                    page
                    pages
                    start
                    end
                    items {
                        class
                        hidden
                        slug: slugs(language: $language) {
                            value
                            language
                        }
                        ... on Product {
                            urlId: productId
                            lastModifiedAt
                            manufacturer
                        }
                        ... on Cluster {
                            urlId: clusterId
                        }
                    }

                }
            }        
        ';

        $return = new stdClass();
        $return->query = $gql;
        $return->variables = [
            'offset' => $offset,
            'page' => $page,
            'language' => $language
        ];
        
        return $return;
    }
}