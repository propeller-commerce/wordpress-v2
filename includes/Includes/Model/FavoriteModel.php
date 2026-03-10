<?php

namespace Propeller\Includes\Model;

if ( ! defined( 'ABSPATH' ) ) exit;

use stdClass;

class FavoriteModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_favorites_lists($lists_args)
    {
        $base_catalog_id = defined('PROPELLER_BASE_CATALOG') ? (int) PROPELLER_BASE_CATALOG : null;

        $gql = '
            query WPFavoriteListsQuery(
                $lists_args: FavoriteListsSearchInput
                $categoryId: Int
            ){
                favoriteLists(input: $lists_args) {
                    items {
                        ... WPFavoriteListFragment
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
            self::favorite_list_fragment()->query,
            $gql
        ];

        $return = new stdClass();
        $return->query = implode("\n\n", $queries);
        $return->variables = [
            'lists_args' => $lists_args,
            'categoryId' => $base_catalog_id
        ];

        return $return;
    }

    public static function favorite_list_fragment()
    {
        $gql = '
            fragment WPFavoriteListFragment on FavoriteList {
                id
                name
                companyId
                contactId
                customerId
                isDefault
                slug
                createdAt
                updatedAt
                products(input: {categoryId: $categoryId}) {
                    itemsFound
                }
                clusters(input: {categoryId: $categoryId}) {
                    itemsFound
                }
            }
        ';

        $return = new stdClass();
        $return->query = $gql;

        return $return;
    }

    public function get_favorite_list($list_id, $images_args, $language, $products_page = 1, $products_offset = 12, $clusters_page = 1, $clusters_offset = 12)
    {
        $product_track_attributes = self::product_track_attributes();
        $base_catalog_id = defined('PROPELLER_BASE_CATALOG') ? (int) PROPELLER_BASE_CATALOG : null;

        $gql = '
            query WPFavoriteListQuery(
                $list_id: String!
                $img_search: MediaImageProductSearchInput
                $img_transform: TransformationsInput!
                $language: String
                $product_track_attrs_filter: AttributeResultSearchInput
                $price_input: PriceCalculateProductInput
                $products_page: Int
                $products_offset: Int
                $clusters_page: Int
                $clusters_offset: Int
                $categoryId: Int
            ){
                favoriteList(id: $list_id) {
                    ... WPFavoriteListFragment
                }
            }        
        ';

        $queries = [
            self::favorites_data_fragment_with_pagination()->query,
            // ProductModel::products_filters_fragment()->query,
            ProductModel::product_inventory_fragment()->query,
            ProductModel::product_price_fragment()->query,
            self::attributes_fragment()->query,
            $images_args->query,

            $gql
        ];

        $return = new stdClass();
        $return->query = implode("\n\n", $queries);
        $return->variables = array_merge(
            [
                'list_id' => $list_id,
                'language' => $language,
                'taxZone' => PROPELLER_DEFAULT_TAXZONE,
                // Use empty object instead of empty array for GraphQL input type
                'product_track_attrs_filter' => !empty($product_track_attributes->variables) ? $product_track_attributes->variables : (object)[],
                'products_page' => $products_page,
                'products_offset' => $products_offset,
                'clusters_page' => $clusters_page,
                'clusters_offset' => $clusters_offset,
                'categoryId' => $base_catalog_id
            ],
            $images_args->variables
        );

        return $return;
    }

    public function create_favorites_list($favlist_args)
    {
        $gql = '
            mutation WPFavoriteListCreateMutation(
                $favlist_args: FavoriteListsCreateInput!
            ){
                favoriteListCreate(input: $favlist_args) {
                    id
                }
            }
        ';

        $return = new stdClass();
        $return->query = $gql;
        $return->variables = [
            'favlist_args' => $favlist_args
        ];

        return $return;
    }

    public function rename_favorite_list($list_id, $list_params)
    {
        $gql = '
            mutation WPFavoriteListRenameMutation(
                $list_id: String! 
                $list_params: FavoriteListsUpdateInput!
            ){
                favoriteListUpdate(id: $list_id, input: $list_params) {
                    id
                }
            }
        ';

        $return = new stdClass();
        $return->query = $gql;
        $return->variables = [
            'list_id' => $list_id,
            'list_params' => $list_params
        ];

        return $return;
    }

    public function delete_favorites_list($list_id)
    {
        $gql = '
            mutation WPFavoriteListDeleteMutation(
                $list_id: String!
            ){
                favoriteListDelete(id: $list_id)
            }
        ';

        $return = new stdClass();
        $return->query = $gql;
        $return->variables = [
            'list_id' => $list_id
        ];

        return $return;
    }

    public function add_favorite($list_id, $list_args, $images_args, $language, $update_favorites = false)
    {
        $product_track_attributes = self::product_track_attributes();
        $base_catalog_id = defined('PROPELLER_BASE_CATALOG') ? (int) PROPELLER_BASE_CATALOG : null;

        $gql = '
            mutation WPFavListAddItemMutation(
                $list_id: String!
                $list_args: FavoriteListsItemsInput!
                $img_search: MediaImageProductSearchInput
                $img_transform: TransformationsInput!
                $language: String
                $product_track_attrs_filter: AttributeResultSearchInput
                $price_input: PriceCalculateProductInput
                $categoryId: Int
               
            ){
                favoriteListAddItems(id: $list_id input: $list_args) {
                    ... WPFavoriteListFragment
                }
            }
        ';

        $queries = [
            self::favorites_data_fragment()->query,
            ProductModel::product_inventory_fragment()->query,
            ProductModel::product_price_fragment()->query,
            self::attributes_fragment()->query,
            $images_args->query,

            $gql
        ];

        $return = new stdClass();
        $return->query = implode("\n\n", $queries);
        $return->variables = array_merge(
            [
                'list_id' => $list_id,
                'list_args' => $list_args,
                'language' => $language,
                'taxZone' => PROPELLER_DEFAULT_TAXZONE,
                'product_track_attrs_filter' => !empty($product_track_attributes->variables) ? $product_track_attributes->variables : (object)[],
                'categoryId' => $base_catalog_id
            ],
            $images_args->variables
        );

        return $return;
    }

    public function delete_favorite($list_id, $list_args)
    {
        $gql = '
            mutation WPFavoriteListRemoveProduct(
                $list_id: String!
                $list_args: FavoriteListsItemsInput!
            ) {
                favoriteListRemoveItems(id: $list_id input: $list_args) {
                    id
                }
            }
        ';

        $return = new stdClass();
        $return->query = $gql;
        $return->variables = [
            'list_id' => $list_id,
            'list_args' => $list_args,
        ];

        return $return;
    }

    public function delete_favorite_multiple_lists($list_ids, $list_args)
    {
        $gqls = [];
        $ids = [];
        $ids_vals = [];

        $index = 0;

        foreach ($list_ids as $list_id) {
            $gqls[] = "
                favRemoveList_$index: favoriteListRemoveItems(id: \$list_id_$index input: \$list_args) {
                    id
                }
            ";

            $ids[] = "\$list_id_$index: String!";
            $ids_vals["list_id_$index"] = $list_id;

            $index++;
        }

        $queries = implode(PHP_EOL, $gqls);
        $ids_arg_defs = implode(PHP_EOL, $ids);

        $gql = "
            mutation WPFavoriteListsRemoveProduct(
                $ids_arg_defs
                \$list_args: FavoriteListsItemsInput!
            ) {
                $queries
            }
        ";

        $return = new stdClass();
        $return->query = $gql;
        $return->variables = array_merge(
            $ids_vals,
            [
                'list_args' => $list_args,
            ]
        );

        return $return;
    }

    public static function favorites_data_fragment()
    {
        $gql = '
            fragment WPFavoriteListFragment on FavoriteList {
                id
                name
                companyId
                contactId
                customerId
                isDefault
                slug
                createdAt
                updatedAt
                products(input: {categoryId: $categoryId}) {
                    ... WPGridProductsFragment
                    itemsFound
                    offset
                    page
                    pages
                    start
                    end
                }
                clusters(input: {categoryId: $categoryId}) {
                    items {
                        ... WPBaseClusterFragment
                        ... on Cluster {
                            class
                            clusterId
                            urlId: clusterId
                            defaultProduct {
                                ... WPGridProductFragment
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
            }
        ';

        $queries = [
            ProductModel::products_grid_fragment(false, false)->query,
            ProductModel::grid_product_fragment(false)->query,
            ProductModel::base_cluster_fragment()->query,
            ProductModel::product_surcharge_fragment()->query,

            $gql
        ];

        $return = new stdClass();
        $return->query = implode("\n\n", $queries);

        return $return;
    }

    public static function favorites_data_fragment_with_pagination()
    {
        $gql = '
            fragment WPFavoriteListFragment on FavoriteList {
                id
                name
                companyId
                contactId
                customerId
                isDefault
                slug
                createdAt
                updatedAt
                products(input: {page: $products_page, offset: $products_offset, categoryId: $categoryId}) {
                    ... WPGridProductsFragment
                    itemsFound
                    offset
                    page
                    pages
                    start
                    end
                }
                clusters(input: {page: $clusters_page, offset: $clusters_offset, categoryId: $categoryId}) {
                    items {
                        ... WPBaseClusterFragment
                        ... on Cluster {
                            class
                            clusterId
                            urlId: clusterId
                            defaultProduct {
                                ... WPGridProductFragment
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
            }
        ';

        $queries = [
            ProductModel::products_grid_fragment(false, false)->query,
            ProductModel::grid_product_fragment(false)->query,
            ProductModel::base_cluster_fragment()->query,
            ProductModel::product_surcharge_fragment()->query,

            $gql
        ];

        $return = new stdClass();
        $return->query = implode("\n\n", $queries);

        return $return;
    }
}
