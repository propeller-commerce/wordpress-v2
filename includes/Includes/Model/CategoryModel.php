<?php

namespace Propeller\Includes\Model;

if ( ! defined( 'ABSPATH' ) ) exit;

use Propeller\Includes\Controller\SessionController;
use Propeller\Includes\Controller\UserController;
use Propeller\Includes\Enum\UserTypes;
use stdClass;

class CategoryModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_catalog($category_args, $product_args, $attributes_args, $images_args, $language)
    {
        $category_track_attributes = self::category_track_attributes();

        $category_track_attrs_arg = !empty($category_track_attributes->query) ? "\$category_track_attrs_filter: AttributeResultSearchInput!" : "";

        // Fix: Make product track attributes filter optional (not required)
        $product_track_attributes = ProductModel::product_track_attributes();

        $tax_zone = PROPELLER_DEFAULT_TAXZONE;

        $category_input = (isset($category_args['categoryId']) && is_numeric($category_args['categoryId']))
            ? "\$categoryId: Float"
            : "\$slug: String";
        $category_argument = (isset($category_args['categoryId']) && is_numeric($category_args['categoryId']))
            ? "categoryId: \$categoryId"
            : "slug: \$slug";

        $product_fav_lists = ProductModel::product_favorite_lists_fragment();

        $fav_list_query = '';
        if (UserController::is_propeller_logged_in()) {
            $fav_list_query = '
                favoriteLists (input: $fav_list_input){ 
                    ... WPProductFavoriteListsFragment
                }
            ';
        }

        $fav_list_input = '';
        if (UserController::is_propeller_logged_in())
            $fav_list_input = '$fav_list_input: FavoriteListsSearchInput';

        $gql = "
            query WPCategoryQuery(
                $category_input
                $fav_list_input
                \$language: String
                \$img_search: MediaImageProductSearchInput
                \$img_transform: TransformationsInput!
                \$product_track_attrs_filter: AttributeResultSearchInput
                $category_track_attrs_arg
                \$product_search: CategoryProductSearchInput
                \$price_input: PriceCalculateProductInput
                \$filters_input: FilterAvailableAttributeInput
            ){
                category($category_argument) {
                    categoryId
                    urlId: categoryId
                    name(language: \$language) {
                        value
                        language
                    }
                    description(language: \$language) {
                        value
                        language
                    }
                    shortDescription(language: \$language) {
                        value
                        language
                    }
                    slug(language: \$language) {
                        value
                        language
                    }
                    metadataTitles {
                        language
                        value
                    }
                    metadataDescriptions {
                        language
                        value
                    }
                    metadataKeywords {
                        language
                        value
                    }
                    metadataCanonicalUrls {
                        language
                        value
                    }
                    categoryPath {
                        ... WPCategoryMinimalFragment
                    }
                    parent {
                        ... WPCategoryMinimalFragment
                    }
                    slugs: slug {
                        value
                        language
                    }
                    $category_track_attributes->query
                    products (input: \$product_search) {
                        ... WPGridProductsFragment
                    }
                }
            }        
        ";

        $product_grid_fragment = ProductModel::products_grid_fragment(true);

        $queries = [
            $images_args->query,
            self::category_minimal_fragment()->query,
            $product_grid_fragment->query,
            ProductModel::grid_product_fragment()->query,
            ProductModel::products_filters_fragment()->query,
            ProductModel::product_inventory_fragment()->query,
            ProductModel::product_price_fragment()->query,
            ProductModel::product_surcharge_fragment()->query,
            defined('PROPELLER_USE_LANGUAGE_IN_ATTRIBUTES') && PROPELLER_USE_LANGUAGE_IN_ATTRIBUTES
                ? self::attributes_lang_fragment()->query
                : self::attributes_fragment()->query,
            $product_fav_lists->query,
            $gql
        ];

        if (!isset($product_args['page']))
            $product_args['page'] = 1;

        if (!isset($product_args['sortInputs']))
            $product_args['sortInputs'] = [
                "field" => "CATEGORY_ORDER",
                "order" => "ASC"
            ];

        $variables = array_merge(
            [
                'language' => $language,
                'product_track_attrs_filter' => count($product_track_attributes->variables) ? $product_track_attributes->variables : new stdClass(),
                'category_track_attrs_filter' => count($category_track_attributes->variables) ? $category_track_attributes->variables : new stdClass(),
                'product_search' => $product_args,
                'price_input' => ProductModel::price_input_array(PROPELLER_DEFAULT_TAXZONE),
                'filters_input' => [
                    'isSearchable' => true
                ]
            ],
            $category_args,
            $attributes_args,
            $images_args->variables,
            $product_fav_lists->variables
        );

        $return = new stdClass();
        $return->query = implode("\n\n", $queries);
        $return->variables = $variables;

        return $return;
    }

    public static function category_minimal_fragment()
    {
        $gql = '
            fragment WPCategoryMinimalFragment on Category {
                categoryId
                urlId: categoryId
                name(language: $language) {
                    language
                    value
                }
                slug(language: $language) {
                    language
                    value
                }
            }
        ';

        $return = new stdClass();
        $return->query = $gql;

        return $return;
    }
}
