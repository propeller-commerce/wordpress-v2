<?php

namespace Propeller\Includes\Model;

if (! defined('ABSPATH')) exit;

use Propeller\Includes\Controller\SessionController;
use Propeller\Includes\Controller\UserController;
use Propeller\Includes\Enum\UserTypes;
use stdClass;

class ProductModel extends BaseModel
{
    public function __construct() {}

    public function get_product($product_args, $images_args, $attributes_args, $language)
    {
        $product_input = isset($product_args['productId']) ? "\$productId: Int" : "\$slug: String";
        $product_arguments = isset($product_args['productId']) ? "productId: \$productId" : "slug: \$slug";

        $product_fav_lists = self::product_favorite_lists_fragment();
        $product_track_attributes = self::product_track_attributes();

        if (!defined('PROPELLER_DEFAULT_TAXZONE'))
            UserController::set_default_tax_zone();

        $taxZone = PROPELLER_DEFAULT_TAXZONE;

        $fav_list_input = '';
        if (UserController::is_propeller_logged_in())
            $fav_list_input = '$fav_list_input: FavoriteListsSearchInput';

        $gql = "
        query WPProductQuery(
            $product_input
            $fav_list_input
            \$language: String
            \$crossUpsell: CrossupsellSearchInput
            \$img_search: MediaImageProductSearchInput
            \$img_transform: TransformationsInput!
            \$product_track_attrs_filter: AttributeResultSearchInput
            \$price_input: PriceCalculateProductInput
          ) {
            product($product_arguments, language: \$language) {
              ... WPBaseProductFragment
              crossupsellsFrom(input: \$crossUpsell) {
                ... WPCrossUpsellsFragment
              }
              crossupsellsTo(input: \$crossUpsell) {
                ... WPCrossUpsellsFragment
              }
            
              ... on Product {
                
                ...WPProductFragment
                bundles {
                  ...WPBundlesFragment
                }
                category {
                  ...WPCategoryMinimalFragment
                }
              }
            }
          } 
        ";

        $queries = [
            $images_args->query,
            CategoryModel::category_minimal_fragment()->query,
            defined('PROPELLER_USE_LANGUAGE_IN_ATTRIBUTES') && PROPELLER_USE_LANGUAGE_IN_ATTRIBUTES
                ? self::attributes_lang_fragment()->query
                : self::attributes_fragment()->query,
            self::base_product_fragment()->query,
            self::product_inventory_fragment()->query,
            self::product_price_fragment()->query,
            self::product_price_data_fragment()->query,
            self::product_fragment()->query,
            self::bundles_fragment()->query,
            self::cross_upsells_fragment()->query,
            self::base_product_grid_fragment()->query,
            self::grid_product_fragment()->query,
            self::product_surcharge_fragment()->query,
            $product_fav_lists->query,

            $gql
        ];

        $variables = array_merge(
            [
                'language' => $language,
                'product_track_attrs_filter' => count($product_track_attributes->variables) ? $product_track_attributes->variables : new stdClass(),
                "crossUpsell" => [
                    "offset" => 100,
                    "page" => 1
                ],
                'price_input' => ProductModel::price_input_array($taxZone)
            ],
            $attributes_args,
            $product_fav_lists->variables,
            $images_args->variables,
            $product_args
        );

        $return = new stdClass();
        $return->query = implode("\n\n", $queries);
        $return->variables = $variables;

        return $return;
    }

    public function get_cluster($cluster_args, $attributes_args, $images_args, $language)
    {
        $cluster_input = isset($cluster_args['clusterId']) ? "\$clusterId: Int" : "\$slug: String";
        $cluster_arguments = isset($cluster_args['clusterId']) ? "clusterId: \$clusterId" : "slug: \$slug";
        $product_track_attributes = self::product_track_attributes();
        $product_fav_lists = self::product_favorite_lists_fragment();

        if (!defined('PROPELLER_DEFAULT_TAXZONE'))
            UserController::set_default_tax_zone();

        $taxZone = PROPELLER_DEFAULT_TAXZONE;

        $cluster_attributes = $this->cluster_attributes_query($attributes_args);

        $attributes_filter = '';
        if (!empty($cluster_attributes->query))
            $attributes_filter = '$attributes_filter: AttributeResultSearchInput!';

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
            query WPClusterQuery(
                $cluster_input
                $fav_list_input
                \$language: String
                \$crossUpsell: CrossupsellSearchInput
                \$img_search: MediaImageProductSearchInput
                \$img_transform: TransformationsInput!
                \$product_track_attrs_filter: AttributeResultSearchInput
                $attributes_filter
                \$price_input: PriceCalculateProductInput
            ){
                cluster($cluster_arguments language: \$language) {
                    ... WPBaseClusterFragment
                    ... on Cluster {
                        class
                        clusterId
                        urlId: clusterId
                        category {
                            ...WPCategoryMinimalFragment
                        }
                        categoryPath {
                            ...WPCategoryMinimalFragment
                        }
                        defaultProduct {
                            productId
                        }
                        trackAttributes: attributes(input: \$product_track_attrs_filter) {
                            ...WPAttributesFragment
                        }
                        $fav_list_query
                        crossupsellsFrom(input: \$crossUpsell) {
                            ... WPCrossUpsellsFragment
                        }
                        crossupsellsTo(input: \$crossUpsell) {
                            ... WPCrossUpsellsFragment
                        }
                        products {
                            ... WPBaseProductFragment
                            ... on Product {
                                ... WPProductFragment
                                $cluster_attributes->query
                                bundles {
                                    ... WPBundlesFragment
                                }
                            }
                        }
                        options {
                            ... WPClusterOptionsFragment
                        }
                        config {
                            ... WPClusterConfigFragment
                        }
                    }
                }
            }
        ";

        $queries = [
            $images_args->query,
            CategoryModel::category_minimal_fragment()->query,
            defined('PROPELLER_USE_LANGUAGE_IN_ATTRIBUTES') && PROPELLER_USE_LANGUAGE_IN_ATTRIBUTES
                ? self::attributes_lang_fragment()->query
                : self::attributes_fragment()->query,
            self::product_inventory_fragment()->query,
            self::product_price_fragment()->query,
            self::base_product_fragment()->query,
            self::product_fragment()->query,
            self::bundles_fragment()->query,
            self::base_cluster_fragment()->query,
            self::base_product_grid_fragment(false)->query,
            self::grid_product_fragment()->query,
            self::product_surcharge_fragment()->query,
            self::product_price_data_fragment()->query,
            self::cluster_config_fragment()->query,
            self::cross_upsells_fragment()->query,
            self::cluster_options_fragment($cluster_attributes)->query,
            $product_fav_lists->query,

            $gql
        ];

        $variables = array_merge(
            [
                'language' => $language,
                'taxZone' => $taxZone,
                'product_track_attrs_filter' => count($product_track_attributes->variables) ? $product_track_attributes->variables : new stdClass(),
                'attributes_filter' => $attributes_args,
                "crossUpsell" => [
                    "offset" => 100,
                    "page" => 1
                ],
                'price_input' => ProductModel::price_input_array($taxZone)
            ],
            $cluster_args,
            $images_args->variables,
            $product_fav_lists->variables,
        );


        $return = new stdClass();
        $return->query = implode("\n\n", $queries);
        $return->variables = $variables;

        return $return;
    }

    // --- Product fragments & queries
    public static function product_inventory_fragment()
    {
        $gql = '
            fragment WPProductInventoryFragment on ProductInventory {
                totalQuantity
                supplierQuantity
                localQuantity
                balance {
                    quantity
                    warehouseId
                }
            }
        ';

        $return = new stdClass();
        $return->query = $gql;

        return $return;
    }

    public static function product_price_fragment()
    {
        $gql = '
            fragment WPProductPriceFragment on ProductPrice {
                net
                gross
                discount {
                    value
                    quantityFrom
                    validFrom
                    validTo
                }
                type
                taxCode
            }
        ';

        $return = new stdClass();
        $return->query = $gql;

        return $return;
    }

    public static function price_input_array($tax_zone)
    {
        $price_input_array = [
            'taxZone' => $tax_zone,
        ];

        if (UserController::is_propeller_logged_in()) {
            if (SessionController::get(PROPELLER_USER_DATA)->__typename == UserTypes::CONTACT) {
                $price_input_array['contactId'] = SessionController::get(PROPELLER_USER_DATA)->userId;
                $price_input_array['companyId'] = SessionController::get(PROPELLER_CONTACT_COMPANY_ID);
            } else {
                $price_input_array['customerId'] = SessionController::get(PROPELLER_USER_DATA)->userId;
            }
        }

        return $price_input_array;
    }

    public static function product_price_data_fragment()
    {
        $gql = '
            fragment WPProductPriceData on Price {
                per
                costPrices {
                    id
                    priceId
                    createdAt
                    lastModifiedAt
                    quantityFrom
                    value
                } 
                list
                suggested
                store
                bulkPriceDiscountType
                defaultTaxCode
                display
            }
        ';

        $return = new stdClass();
        $return->query = $gql;

        return $return;
    }

    public static function base_product_fragment()
    {
        $gql = '
        fragment WPBaseProductFragment on Product {
            class
            sku
            name: names(language: $language) {
                value
                language
            }
            description: descriptions(language: $language) {
                value
                language
            }
            shortDescription: shortDescriptions(language: $language) {
                value
                language
            }                   
            slug: slugs(language: $language) {
                value
                language
            }
            slugs {
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
            category {
                ... WPCategoryMinimalFragment
            }
            categoryPath {
                ... WPCategoryMinimalFragment
            }
          }
        ';

        $return = new stdClass();
        $return->query = $gql;

        return $return;
    }

    public static function base_product_grid_fragment()
    {
        $gql = '
        fragment WPBaseProductGridFragment on Product {
            class
            sku
            name: names(language: $language) {
                value
                language
            }
            slug: slugs(language: $language) {
                value
                language
            }
            slugs {
                value
                language
            }
          }
        ';

        $return = new stdClass();
        $return->query = $gql;

        return $return;
    }

    public static function base_cluster_fragment()
    {
        $gql = '
        fragment WPBaseClusterFragment on Cluster {
            class
            sku
            name: names(language: $language) {
                value
                language
            }
            description: descriptions(language: $language) {
                value
                language
            }
            shortDescription: shortDescriptions(language: $language) {
                value
                language
            }                    
            slug: slugs(language: $language) {
                value
                language
            }
            slugs {
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
          }
        ';

        $return = new stdClass();
        $return->query = $gql;

        return $return;
    }

    public static function product_fragment()
    {
        $fav_list_query = '';
        if (UserController::is_propeller_logged_in()) {
            $fav_list_query = '
                favoriteLists (input: $fav_list_input){ 
                    ... WPProductFavoriteListsFragment
                }
            ';
        }

        $gql = "
            fragment WPProductFragment on Product {
                productId
                urlId: productId
                shortNames(language: \$language) {
                    value
                    language
                }
                manufacturerCode
                eanCode
                manufacturer
                supplier
                supplierCode
                status
                orderable
                hasBundle
                isBundleLeader
                minimumQuantity
                unit
                package
                purchaseUnit
                purchaseMinimumQuantity
                packageDescriptions {
                    value
                    language
                } 
                customKeywords {
                    language
                    value
                }
                inventory {
                    ...WPProductInventoryFragment
                }
                price(input: \$price_input) {
                    ...WPProductPriceFragment
                }
                priceData {
                    ...WPProductPriceData
                }
                bulkPrices {
                  discount {
                    value
                    quantityFrom
                    validFrom
                    validTo
                  }
                  net
                  gross
                }
                trackAttributes: attributes(input: \$product_track_attrs_filter) {
                    ...WPAttributesFragment
                }
                media {
                    ...WPProductImageFragment
                }
                surcharges {
                    ...WPProductSurchargeFragment
                }
                $fav_list_query
            }
        ";

        $return = new stdClass();
        $return->query = $gql;

        return $return;
    }

    public static function product_favorite_lists_fragment()
    {
        $return = new stdClass();
        $return->query = '';
        $return->variables = [];

        if (!UserController::is_propeller_logged_in()) {
            return $return;
        } else {
            $user = SessionController::get(PROPELLER_USER_DATA);
            $typename = $user->__typename == UserTypes::CUSTOMER ? 'customerId' : 'contactId';

            $return->variables = [
                'fav_list_input' => [
                    $typename => $user->userId
                ]
            ];

            $gql = "
                fragment WPProductFavoriteListsFragment on FavoriteListsResponse {
                    itemsFound
                    items {
                        id
                    }
                }
            ";

            $return->query = $gql;
        }

        return $return;
    }

    public static function grid_product_fragment($include_favlists = true)
    {
        $fav_list_query = '';
        if (UserController::is_propeller_logged_in() && $include_favlists) {
            $fav_list_query = '
                favoriteLists (input: $fav_list_input){ 
                    ... WPProductFavoriteListsFragment
                }
            ';
        }

        $gql = "
            fragment WPGridProductFragment on Product {
                productId
                urlId: productId
                sku
                createdAt
                manufacturer
                status
                orderable
                minimumQuantity
                unit
                package
                purchaseUnit
                purchaseMinimumQuantity
                packageDescriptions {
                    value
                    language
                } 
                customKeywords {
                    language
                    value
                }
                inventory {
                    ...WPProductInventoryFragment
                }
                price(input: \$price_input) {
                    ...WPProductPriceFragment
                }
                priceData {
                    display
                    suggested
                }
                categoryPath {
                    categoryId
                    name(language: \$language) {
                        value
                        language
                    }
                }
                trackAttributes: attributes(input: \$product_track_attrs_filter) {
                    ...WPAttributesFragment
                }
                media {
                    ...WPProductImageFragment
                }
                surcharges {
                    ...WPProductSurchargeFragment
                }
                $fav_list_query
            }
        ";

        $return = new stdClass();
        $return->query = $gql;

        return $return;
    }

    public static function grid_cluster_fragment($include_favlists = true)
    {
        $gql = '
            fragment WPGridClusterFragment on Cluster {
                clusterId
                urlId: clusterId
            }
        ';

        $return = new stdClass();
        $return->query = $gql;

        return $return;
    }

    public static function cross_upsells_fragment($include_favlists = false)
    {
        $gql = '
            fragment WPCrossUpsellsFragment on CrossupsellsResponse {
                items {
                    ... WPCrossUpsellFragment
                }
                itemsFound
                offset
                page
                pages
                start
                end
            }
        ';

        $queries = [
            self::cross_upsell_fragment($include_favlists)->query,
            $gql
        ];

        $return = new stdClass();
        $return->query = implode("\n\n", $queries);

        return $return;
    }

    public static function cross_upsell_fragment($include_favlists = false)
    {
        $fav_list_query = '';
        if (UserController::is_propeller_logged_in() && $include_favlists) {
            $fav_list_query = '
                favoriteLists (input: $fav_list_input){ 
                    ... WPProductFavoriteListsFragment
                }
            ';
        }

        $gql = "
            fragment WPCrossUpsellFragment on Crossupsell {
                id
                type
                subType
                productTo {
                    ... WPBaseProductGridFragment
                    ... WPGridProductFragment
                }
                clusterTo {
                    ...WPBaseProductGridFragment
                    ... on Cluster {
                        class
                        clusterId
                        urlId: clusterId
                        sku
                        $fav_list_query
                        name: names(language: \$language) {
                            value
                            language
                        }
                        slug: slugs(language: \$language) {
                            value
                            language
                        }
                        slugs {
                            value
                            language
                        }
                        defaultProduct {
                            ...WPGridProductFragment
                        }
                    }
                }
            }
        ";

        $return = new stdClass();
        $return->query = $gql;

        return $return;
    }

    public static function bundles_fragment()
    {
        $gql = '
        fragment WPBundlesFragment on Bundle {
            id
            name
            description
            condition
            discount
            price {
                gross
                net
                originalGross
                originalNet
            }
            items {
                isLeader
                price {
                gross
                net
                }
                product {
                    class
                    name: names(language: $language) {
                        value
                        language
                    }
                    sku
                    slug: slugs(language: $language) {
                        value
                        language
                    }
                    ... on Product {
                        ...WPGridProductFragment
                    }
                }
            }
        }
        ';

        $return = new stdClass();
        $return->query = $gql;

        return $return;
    }

    public static function product_surcharge_fragment()
    {
        $gql = '
            fragment WPProductSurchargeFragment on Surcharge {
                id
                name {
                    value
                    language
                }
                description {
                    value
                    language
                }
                type
                value
                taxCode
                taxZone
                enabled
                validFrom
                validTo
            }
        ';

        $return = new stdClass();
        $return->query = $gql;

        return $return;
    }

    public function specifications($product_id, $attributes_args)
    {
        $attributes_query = self::attributes()->query;

        $gql = "
            query WPProductSpecifications(
                \$productId: Int
                \$language: String
                \$attributes_filter: AttributeResultSearchInput!
            ){
                product(productId: \$productId language: \$language) {
                    productId
                    eanCode
                    manufacturer
                    packageDescriptions {
                        value
                        language
                    } 
                    $attributes_query
                }
            }
        ";

        $queries = [
            defined('PROPELLER_USE_LANGUAGE_IN_ATTRIBUTES') && PROPELLER_USE_LANGUAGE_IN_ATTRIBUTES
                ? self::attributes_lang_fragment()->query
                : self::attributes_fragment()->query,
            $gql
        ];

        $return = new stdClass();
        $return->query = implode("\n\n", $queries);
        $return->variables = [
            'productId' => $product_id,
            'language' => strtoupper(PROPELLER_LANG),
            'attributes_filter' => $attributes_args
        ];

        return $return;
    }

    public function downloads($product_id, $downloads_args)
    {
        $gql = "
            query WPProductDownloads(
                \$productId: Int
                \$language: String
                \$doc_search: MediaDocumentProductSearchInput
            ){
                product(productId: \$productId) {
                    media {
                        ...WPProductDocumentFragment
                    }
                }
            }
        ";

        $queries = [
            $downloads_args->query,
            $gql
        ];

        $return = new stdClass();
        $return->query = implode("\n\n", $queries);
        $return->variables = array_merge(
            ['productId' => $product_id],
            $downloads_args->variables
        );

        return $return;
    }

    public function videos($product_id, $videos_args)
    {
        $gql = "
            query WPProductVideos(
                \$productId: Int
                \$language: String
                \$vid_search: MediaVideoProductSearchInput
            ){
                product(productId: \$productId) {
                    media {
                        ...WPProductVideoFragment
                    }
                }
            }
        ";

        $queries = [
            $videos_args->query,
            $gql
        ];

        $return = new stdClass();
        $return->query = implode("\n\n", $queries);
        $return->variables = array_merge(
            ['productId' => $product_id],
            $videos_args->variables
        );

        return $return;
    }

    public static function products_grid_fragment($include_filters = true, $include_favlists = true)
    {
        $product_track_attributes = self::product_track_attributes();

        $filters_fragment = '';
        if ($include_filters)
            $filters_fragment = 'filters(input: $filters_input) {
                ... WPProductFiltersFragment
            }';

        $fav_list_query = '';
        if (UserController::is_propeller_logged_in() && $include_favlists) {
            $fav_list_query = '
                favoriteLists (input: $fav_list_input){ 
                    ... WPProductFavoriteListsFragment
                }
            ';
        }

        $gql = "
        fragment WPGridProductsFragment on ProductsResponse {
            itemsFound
            offset
            page
            pages
            start
            minPrice
            maxPrice
            end
            items {
                class
                name: names(language: \$language) {
                    value
                    language
                }
                shortDescriptions(language: \$language) {
                    value
                    language
                }
                sku
                hidden
                slug: slugs(language: \$language) {
                    value
                    language
                }
                ... on Product {
                    ... WPGridProductFragment
                }
                ... on Cluster {
                    class
                    clusterId
                    urlId: clusterId
                    defaultProduct {
                        ... WPGridProductFragment
                    }
                    products {
                        ... WPGridProductFragment
                    }
                    options {
                        isRequired
                        defaultProduct {
                            productId
                        }
                        products {
                            class
                            productId
                            ... on Product {
                                price(input: \$price_input) {
                                    ...WPProductPriceFragment
                                }
                            }
                        }
                    }
                    $fav_list_query
                }
            }
            $filters_fragment
        }
        ";

        $return = new stdClass();
        $return->query = $gql;
        $return->variables = $product_track_attributes->variables;

        return $return;
    }

    public static function products_slider_fragment()
    {
        $product_track_attributes = self::product_track_attributes();

        $fav_list_query = '';
        if (UserController::is_propeller_logged_in()) {
            $fav_list_query = '
                favoriteLists (input: $fav_list_input){ 
                    ... WPProductFavoriteListsFragment
                }
            ';
        }

        $gql = "
            fragment WPSliderProductsFragment on ProductsResponse {
                itemsFound
                offset
                page
                pages
                start
                minPrice
                maxPrice
                end
                items {
                    class
                    name: names(language: \$language) {
                        value
                        language
                    }
                    shortDescriptions(language: \$language) {
                        value
                        language
                    }    
                    sku
                    hidden
                    slug: slugs(language: \$language) {
                        value
                        language
                    }
                    ... on Product {
                        ... WPGridProductFragment
                    }
                    ... on Cluster {
                        class
                        clusterId
                        urlId: clusterId
                        $fav_list_query
                        defaultProduct {
                            ... WPGridProductFragment
                        }
                    }
                }
            }
        ";

        $return = new stdClass();
        $return->query = $gql;
        $return->variables = $product_track_attributes->variables;

        return $return;
    }

    public static function products_filters_fragment()
    {
        $gql = '
            fragment WPProductFiltersFragment on AttributeFilter {
                id
                attributeDescription {
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
                    group
                }
                type
                textFilters {
                    value
                    count
                    countTotal
                    countActive
                    isSelected
                }
                integerRangeFilter {
                    min
                    max
                }
                decimalRangeFilter {
                    min
                    max
                }
            }
        ';

        $return = new stdClass();
        $return->query = $gql;

        return $return;
    }

    public static function product_order_fragment()
    {
        $gql = '
        fragment WPProductOrderFragment on Product {
            productId
            urlId: productId
            shortNames(language: $language){
                value
                language
            }
            manufacturerCode
            eanCode
            manufacturer
            supplier
            supplierCode
            status
            orderable
            hasBundle
            isBundleLeader
            minimumQuantity
            unit
            package
            purchaseUnit
            purchaseMinimumQuantity
            packageDescriptions {
                value
                language
            } 
            customKeywords {
                language
                value
            }
            inventory {
               ...WPProductInventoryFragment
            }
            price(input: { taxZone: $taxZone }) {
                ...WPProductPriceFragment
            }
            priceData {
               ...WPProductPriceData
            }
            trackAttributes: attributes(input: $product_track_attrs_filter) {
                ...WPAttributesFragment
            }
            attributes(
                input: { attributeDescription: { isPublic: true, isSearchable: true } }
            ) {
                ...WPAttributesFragment
            }
            media {
                ...WPProductImageFragment
            }
            surcharges {
                ...WPProductSurchargeFragment
            }
          }
        ';

        $queries = [
            self::product_inventory_fragment()->query,
            self::product_price_data_fragment()->query,
            self::product_price_fragment()->query,
            self::attributes_fragment()->query,
            self::product_surcharge_fragment()->query,
            $gql
        ];

        $return = new stdClass();
        $return->query = implode("\n\n", $queries);

        return $return;
    }

    public static function product_shipment_fragment()
    {
        $gql = '
        fragment WPProductShipmentFragment on Product {
            productId
            urlId: productId
            ... WPBaseProductFragment
            media {
                ...WPProductImageFragment
            }
          }
        ';

        $queries = [
            self::base_product_fragment()->query,
            CategoryModel::category_minimal_fragment()->query,

            $gql
        ];

        $return = new stdClass();
        $return->query = implode("\n\n", $queries);

        return $return;
    }
    // --- /Product fragments and queries

    // --- Cluster fragments and queries
    public function cluster_config_fragment()
    {
        $gql = '
            fragment WPClusterConfigFragment on ClusterConfig{
                id 
                name
                settings {
                    id
                    name
                    type
                    displayType
                    priority
                }
            }
            ';

        $return = new stdClass();
        $return->query = $gql;
        $return->variables = [];

        return $return;
    }

    public function get_cluster_attributes($args, $language)
    {
        $cluster_input = isset($args['clusterId']) ? "\$clusterId: Int" : "\$slug: String";
        $cluster_args = isset($args['clusterId']) ? "clusterId: \$clusterId" : "slug: \$slug";

        $gql = "
            query WPClusterAttributes(
                $cluster_input
                \$language: String
            ){
                cluster($cluster_args language: \$language) {
                    config {
                        ... WPClusterConfigFragment
                    }
                }
            }
            ";

        $return = new stdClass();

        $return->query = implode("\n\n", [
            self::cluster_config_fragment()->query,
            $gql
        ]);

        $return->variables = [
            'language' => $language
        ];

        if (isset($args['clusterId']))
            $return->variables["clusterId"] = $args['clusterId'];
        else
            $return->variables["slug"] = $args['slug'];

        return $return;
    }

    public function cluster_attributes_query($attributes_args)
    {
        if (!$attributes_args) {
            $return = new stdClass();
            $return->query = '';
            $return->variables = [];

            return $return;
        }

        return $this->attributes($attributes_args);
    }

    public static function cluster_options_fragment($cluster_attributes)
    {
        $gql = "
            fragment WPClusterOptionsFragment on ClusterOption {
                id
                isRequired
                hidden
                name: names(language: \$language) {
                    language
                    value
                }
                description: descriptions(language: \$language) {
                    language
                    value
                }
                shortDescription: shortDescriptions(language: \$language) {
                    language
                    value
                }
                defaultProduct {
                    productId
                }
                products {
                    class
                    name: names(language: \$language) {
                        value
                        language
                    }
                    description: descriptions(language: \$language) {
                        value
                        language
                    }
                    shortDescription: shortDescriptions(language: \$language) {
                        value
                        language
                    }
                    sku
                    slug: slugs(language: \$language) {
                        value
                        language
                    }
                    ... on Product {
                        ... WPProductFragment
                        $cluster_attributes->query                                  
                    }
                }
            }
        ";

        $return = new stdClass();
        $return->query = $gql;

        return $return;
    }
    // --- /Cluster fragments and queries

    // --- Helper fragments and queries
    public function check_product($args)
    {
        $product_input = isset($args['productId']) ? "\$productId: Int" : "\$slug: String";
        $product_args = isset($args['productId']) ? "productId: \$productId" : "slug: \$slug";

        $gql = "
            query WPCheckProductQuery(
                $product_input 
                \$language: String
            ){
                product($product_args language: \$language) {
                    class
                }
            }
        ";

        $return = new stdClass();
        $return->query = $gql;
        $return->variables = $args;

        return $return;
    }

    public function check_cluster($args)
    {
        $cluster_input = isset($args['clusterId']) ? "\$clusterId: Int" : "\$slug: String";
        $cluster_args = isset($args['clusterId']) ? "clusterId: \$clusterId" : "slug: \$slug";

        $gql = "
            query WPCheckClusterQuery(
                $cluster_input 
                \$language: String
            ){
                cluster($cluster_args language: \$language) {
                    class
                }
            }
        ";

        $return = new stdClass();
        $return->query = $gql;
        $return->variables = $args;

        return $return;
    }

    public function check_product_language($slug, $product_id, $type, $language)
    {
        $check_input = $product_id ? "\$productId: Int" : "\$slug: String";
        $check_args = $product_id ? $type . "Id: \$productId" : "slug: \$slug";

        $gql = "
            query WPCheckProductLanguage(
                $check_input
                \$language: String
            ){
                $type(
                    $check_args
                    language: \$language
                ) {
                    slugs {
                        value
                        language
                    }
                }
            }
        ";

        $return = new stdClass();
        $return->query = $gql;
        $return->variables = [
            'language' => $language
        ];

        if ($product_id)
            $return->variables["productId"] = $product_id;
        else
            $return->variables["slug"] = $slug;

        return $return;
    }
    // --- /Helper fragments and queries


    public function crossupsells($arguments, $images_args, $language, $type, $class)
    {
        if (!defined('PROPELLER_DEFAULT_TAXZONE'))
            UserController::set_default_tax_zone();

        $taxZone = PROPELLER_DEFAULT_TAXZONE;

        $product_track_attributes = $this->product_track_attributes();
        $alias_name = $type;

        $crossupsells_input = "";
        if (isset($arguments['productId']))
            $crossupsells_input = "\$productId: Int";
        else if (isset($arguments['clusterId']))
            $crossupsells_input = "\$clusterId: Int";
        else
            $crossupsells_input = "\$slug: String";

        $input_vars = [];
        $crossupsells_args = "";
        if (isset($arguments['productId'])) {
            $input_vars['productId'] = $arguments['productId'];
            $crossupsells_args = "productId: \$productId";
        } else if (isset($arguments['clusterId'])) {
            $input_vars['clusterId'] = $arguments['clusterId'];
            $crossupsells_args = "clusterId: \$clusterId";
        } else {
            $input_vars['slug'] = $arguments['slug'];
            $crossupsells_args = "slug: \$slug";
        }

        $gql = "
            query WPCrossUpsellsQuery(
                $crossupsells_input
                \$language: String
                \$taxZone: String! = \"NL\"
                \$crossupsell_input: CrossupsellTypesInput
                \$img_search: MediaImageProductSearchInput
                \$img_transform: TransformationsInput!
                \$product_track_attrs_filter: AttributeFilterInput
            ){
                $class($crossupsells_args language: \$language) {
                    $alias_name: crossupsells(input: \$crossupsell_input) {
                        type
                        subtype
                        productId
                        clusterId
                        item {
                            class
                            name(language: \$language) {
                                value
                                language
                            }
                            sku
                            slug(language: \$language) {
                                value
                                language
                            }
                            shortDescription(language: \$language) {
                                value
                                language
                            }
                            ... on Product {
                                ... WPProductFragment
                            }
                            ... on Cluster {
                                class
                                clusterId
                                urlId: clusterId
                                defaultProduct {
                                    ... WPProductFragment
                                }
                            }
                        }
                    }
                    
                }
            }
        ";

        $variables = array_merge(
            [
                'language' => $language,
                'taxZone' => $taxZone,
                'product_track_attrs_filter' => count($product_track_attributes->variables) ? $product_track_attributes->variables : new stdClass(),
                'crossupsell_input' => [
                    "types" => [
                        strtoupper($type)

                    ]
                ]
            ],
            $images_args->variables,
            $input_vars
        );

        $queries = [
            $images_args->query,
            defined('PROPELLER_USE_LANGUAGE_IN_ATTRIBUTES') && PROPELLER_USE_LANGUAGE_IN_ATTRIBUTES
                ? self::attributes_lang_fragment()->query
                : self::attributes_fragment()->query,
            self::product_inventory_fragment()->query,
            self::product_price_fragment()->query,
            self::product_fragment()->query,
            $gql
        ];

        $return = new stdClass();
        $return->query = implode("\n\n", $queries);
        $return->variables = $variables;

        return $return;
    }

    public function get_slider_products($products_args, $images_args, $language)
    {
        if (!defined('PROPELLER_DEFAULT_TAXZONE'))
            UserController::set_default_tax_zone();

        $taxZone = PROPELLER_DEFAULT_TAXZONE;
        $product_track_attributes = self::product_track_attributes();

        $product_fav_lists = ProductModel::product_favorite_lists_fragment();

        $fav_list_input = '';
        if (UserController::is_propeller_logged_in())
            $fav_list_input = '$fav_list_input: FavoriteListsSearchInput';

        $gql = "
            query WPSliderProductsQuery(
                \$product_params: ProductSearchInput
                \$language: String
                \$img_search: MediaImageProductSearchInput
                \$img_transform: TransformationsInput!
                \$product_track_attrs_filter: AttributeResultSearchInput
                \$price_input: PriceCalculateProductInput
                $fav_list_input
            ){
                products(input: \$product_params) {
                    ... WPSliderProductsFragment
                }
            }
        ";

        $variables = array_merge(
            [
                'language' => $language,
                'product_track_attrs_filter' => count($product_track_attributes->variables) ? $product_track_attributes->variables : new stdClass(),
                'product_params' => $products_args,
                'price_input' => self::price_input_array($taxZone)
            ],
            $images_args->variables,
            $product_fav_lists->variables
        );

        $queries = [
            $images_args->query,
            defined('PROPELLER_USE_LANGUAGE_IN_ATTRIBUTES') && PROPELLER_USE_LANGUAGE_IN_ATTRIBUTES
                ? self::attributes_lang_fragment()->query
                : self::attributes_fragment()->query,
            self::product_inventory_fragment()->query,
            self::product_price_fragment()->query,
            self::grid_product_fragment()->query,
            self::products_slider_fragment()->query,
            self::product_surcharge_fragment()->query,
            $product_fav_lists->query,
            $gql
        ];

        $return = new stdClass();
        $return->query = implode("\n\n", $queries);
        $return->variables = $variables;

        return $return;
    }

    public function get_products($products_args, $filters_args, $images_args, $language)
    {
        if (!defined('PROPELLER_DEFAULT_TAXZONE'))
            UserController::set_default_tax_zone();

        $taxZone = PROPELLER_DEFAULT_TAXZONE;
        $base_catalog_id = PROPELLER_BASE_CATALOG;

        $product_track_attributes = self::product_track_attributes();

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
            query WPProductSearchQuery(
                \$products_search: CategoryProductSearchInput
                \$product_track_attrs_filter: AttributeResultSearchInput
                \$base_catalog_id: Float
                \$language: String
                \$img_search: MediaImageProductSearchInput
                \$img_transform: TransformationsInput!
                \$price_input: PriceCalculateProductInput
                \$filters_input: FilterAvailableAttributeInput
                $fav_list_input
            ){
                category(categoryId: \$base_catalog_id) {
                    products(input: \$products_search) {
                        ... WPGridProductsFragment
                    }
                }                
            }        
        ";

        $queries = [
            $images_args->query,
            self::products_filters_fragment()->query,
            self::products_grid_fragment(true)->query,
            self::grid_product_fragment()->query,
            self::product_inventory_fragment()->query,
            self::product_price_fragment()->query,
            defined('PROPELLER_USE_LANGUAGE_IN_ATTRIBUTES') && PROPELLER_USE_LANGUAGE_IN_ATTRIBUTES
                ? self::attributes_lang_fragment()->query
                : self::attributes_fragment()->query,
            self::product_surcharge_fragment()->query,
            $product_fav_lists->query,
            $gql
        ];

        $return = new stdClass();
        $return->query = implode("\n\n", $queries);
        $return->variables = array_merge(
            [
                'base_catalog_id' => $base_catalog_id,
                'language' => $language,
                'products_search' => $products_args,
                'product_track_attrs_filter' => count($product_track_attributes->variables) ? $product_track_attributes->variables : new stdClass(),
                'price_input' => self::price_input_array($taxZone),
                'filters_input' => [
                    'isSearchable' => true
                ]
            ],
            $images_args->variables,
            $product_fav_lists->variables
        );

        return $return;
    }

    public function global_search_products($products_args, $images_args, $language)
    {
        if (!defined('PROPELLER_DEFAULT_TAXZONE'))
            UserController::set_default_tax_zone();

        $taxZone = PROPELLER_DEFAULT_TAXZONE;
        $base_catalog_id = PROPELLER_BASE_CATALOG;

        $gql = "
            query WPAutocompleteQuery(
                \$products_search: CategoryProductSearchInput
                \$base_catalog_id: Float
                \$language: String
                \$taxZone: String = \"NL\"
                \$img_search: MediaImageProductSearchInput
                \$img_transform: TransformationsInput!
            ){
                category(categoryId: \$base_catalog_id) {
                    products(input: \$products_search) {
                        itemsFound
                        items {
                            class
                            sku
                            name: names (language: \$language) {
                                value
                                language
                            }
                            slug: slugs (language: \$language) {
                                value
                                language
                            }
                            ... on Product {
                                productId
                                urlId: productId
                                minimumQuantity
                                supplierCode
                                unit
                                packageDescriptions {
                                    value
                                    language
                                } 
                                customKeywords {
                                    language
                                    value
                                }
                                media {
                                    ... WPProductImageFragment
                                }
                                price(input: { taxZone: \$taxZone }) {
                                    ...WPProductPriceFragment
                                }
                                priceData {
                                    display
                                    suggested
                                }
                            }
                            ... on Cluster {
                                clusterId
                                urlId: clusterId
                                defaultProduct {
                                    media {
                                        ... WPProductImageFragment
                                    }
                                }
                            }
                        }
                    }
                }
            }
        ";

        $queries = [
            $images_args->query,
            self::product_price_fragment()->query,
            $gql
        ];

        $return = new stdClass();
        $return->query = implode("\n\n", $queries);
        $return->variables = array_merge(
            [
                'base_catalog_id' => $base_catalog_id,
                'taxZone' => $taxZone,
                'language' => $language,
                'products_search' => $products_args
            ],
            $images_args->variables
        );

        return $return;
    }

    public function get_product_codes($products_input)
    {
        $gql = '
            query WPProductCodesQuery(
                $products_input: ProductSearchInput
            ) { 
                products(input: $products_input) {
                    itemsFound
                    items {
                        ... on Product {
                            productId
                            manufacturerCode
                            eanCode
                            sku
                            supplierCode
                        } 
                    }                    
                }
            }
        ';

        $return = new stdClass();
        $return->query = $gql;
        $return->variables = [
            'products_input' => [
                'language' => PROPELLER_LANG,
                'offset' => count($products_input),
                'page' => 1,
                'productIds' => $products_input
            ]
        ];

        return $return;
    }

    public function get_cluster_favorite_lists($cluster_args)
    {
        $product_fav_lists = self::product_favorite_lists_fragment();

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
            query WPClusterFavoriteListsQuery(
                \$cluster_id: Int
                $fav_list_input
                \$language: String
            ){
                cluster(clusterId: \$cluster_id language: \$language) {
                    ... on Cluster {
                        $fav_list_query
                    }
                }
            }
        ";

        $queries = [
            $product_fav_lists->query,
            $gql
        ];

        $variables = array_merge(
            $cluster_args,
            $product_fav_lists->variables,
        );


        $return = new stdClass();
        $return->query = implode("\n\n", $queries);
        $return->variables = $variables;

        return $return;
    }
}
