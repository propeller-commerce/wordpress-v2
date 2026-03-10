<?php

namespace Propeller\Includes\Model;

if (! defined('ABSPATH')) exit;

use Propeller\Includes\Controller\UserController;
use Propeller\Includes\Enum\AddressType;
use Propeller\Includes\Enum\AddressTypeCart;
use Propeller\Includes\Enum\CrossupsellTypes;
use stdClass;

class ShoppingCartModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();
    }

    public function cart_start($cart_args, $images_args, $language)
    {
        $product_track_attributes = ProductModel::product_track_attributes();

        $cart_start_input = count($cart_args) ? "\$cart_start_args: CartStartInput" : "";
        $cart_start_arg = count($cart_args) ? "(input: \$cart_start_args)" : "";

        $gql = "
            mutation WPCartStart(
                $cart_start_input
                \$crossupsells_input: CrossupsellSearchInput
                \$img_search: MediaImageProductSearchInput
                \$img_transform: TransformationsInput!
                \$language: String
                \$taxZone: String! = \"NL\"
                \$product_track_attrs_filter: AttributeResultSearchInput
            ){
                cartStart $cart_start_arg {
                    ... WPCartDataFragment
                }
            }
        ";

        $queries = [
            self::cart_data_fragment()->query,
            $images_args->query,
            $gql
        ];

        $return = new stdClass();
        $return->query = implode("\n\n", $queries);
        $return->variables = array_merge(
            $images_args->variables,
            [
                'language' => $language,
                'taxZone' => PROPELLER_DEFAULT_TAXZONE,
                'cart_start_args' => $cart_args,
                'crossupsells_input' => [
                    'types' => [
                        CrossupsellTypes::ACCESSORIES
                    ]
                ],
                'product_track_attrs_filter' => !empty($product_track_attributes->variables) ? $product_track_attributes->variables : (object)[]
            ]
        );

        return $return;
    }

    public function set_user($cart_id, $cart_args, $images_args, $language)
    {
        $product_track_attributes = ProductModel::product_track_attributes();

        $gql = '
            mutation WPCartSetUserMutation(
                $cart_id: String!
                $cart_input: CartSetUserInput!
                $crossupsells_input: CrossupsellSearchInput
                $img_search: MediaImageProductSearchInput
                $img_transform: TransformationsInput!
                $language: String
                $taxZone: String! = "NL"
                $product_track_attrs_filter: AttributeResultSearchInput
            ){
                cartSetUser(id: $cart_id input: $cart_input) {
                    ... WPCartDataFragment
                }
            }        
        ';

        $queries = [
            self::cart_data_fragment()->query,
            $images_args->query,
            $gql
        ];

        $return = new stdClass();
        $return->query = implode("\n\n", $queries);
        $return->variables = array_merge(
            $images_args->variables,
            [
                'language' => $language,
                'taxZone' => PROPELLER_DEFAULT_TAXZONE,
                'cart_input' => $cart_args,
                'cart_id' => $cart_id,
                'crossupsells_input' => [
                    'types' => [
                        CrossupsellTypes::ACCESSORIES
                    ]
                ],
                'product_track_attrs_filter' => !empty($product_track_attributes->variables) ? $product_track_attributes->variables : (object)[]
            ]
        );

        return $return;
    }

    public function add_item($cart_id, $cart_args, $images_args, $language)
    {
        $product_track_attributes = ProductModel::product_track_attributes();

        $gql = '
            mutation WPCartAddItemMutation(
                $cart_id: String!
                $cart_input: CartAddItemInput!
                $crossupsells_input: CrossupsellSearchInput
                $img_search: MediaImageProductSearchInput
                $img_transform: TransformationsInput!
                $language: String
                $taxZone: String! = "NL"
                $product_track_attrs_filter: AttributeResultSearchInput
            ){
                cartAddItem(id: $cart_id input: $cart_input) {
                    ... WPCartDataFragment
                }
            }
        ';

        $queries = [
            self::cart_data_fragment()->query,
            $images_args->query,
            $gql
        ];

        $return = new stdClass();
        $return->query = implode("\n\n", $queries);
        $return->variables = array_merge(
            $images_args->variables,
            [
                'language' => $language,
                'taxZone' => PROPELLER_DEFAULT_TAXZONE,
                'cart_input' => $cart_args,
                'cart_id' => $cart_id,
                'crossupsells_input' => [
                    'types' => [
                        CrossupsellTypes::ACCESSORIES
                    ]
                ],
                'product_track_attrs_filter' => !empty($product_track_attributes->variables) ? $product_track_attributes->variables : (object)[]
            ]
        );

        return $return;
    }

    public function bulk_cart_items($bulk_cart_input)
    {
        $gql = '
            mutation WPBulkCartItems (
                $bulk_input: CartItemsBulkUpsertInput!
            ) {
                cartItemBulk (input: $bulk_input) {
                    created
                    total
                    updated
                }
            }
        ';

        $return = new stdClass();
        $return->query = $gql;
        $return->variables = [
            'bulk_input' => $bulk_cart_input
        ];

        return $return;
    }

    public function add_item_replenish($product_id)
    {
        $input_var = "\$add_item_" . $product_id;

        $gql = "
            addItem_$product_id: cartAddItem(id: \$cart_id input: $input_var) {
                cartId
            }
        ";

        return $gql;
    }


    public function add_item_bundle($cart_id, $cart_args, $images_args, $language)
    {
        $product_track_attributes = ProductModel::product_track_attributes();

        $gql = '
            mutation WPCartAddBundleMutation(
                $cart_id: String!
                $cart_input: CartAddBundleInput!
                $crossupsells_input: CrossupsellSearchInput
                $img_search: MediaImageProductSearchInput
                $img_transform: TransformationsInput!
                $language: String
                $taxZone: String! = "NL"
                $product_track_attrs_filter: AttributeResultSearchInput
            ){
                cartAddBundle(id: $cart_id input: $cart_input) {
                    ... WPCartDataFragment
                }
            }
        ';

        $queries = [
            self::cart_data_fragment()->query,
            $images_args->query,
            $gql
        ];

        $return = new stdClass();
        $return->query = implode("\n\n", $queries);
        $return->variables = array_merge(
            $images_args->variables,
            [
                'language' => $language,
                'taxZone' => PROPELLER_DEFAULT_TAXZONE,
                'cart_input' => $cart_args,
                'cart_id' => $cart_id,
                'crossupsells_input' => [
                    'types' => [
                        CrossupsellTypes::ACCESSORIES
                    ]
                ],
                'product_track_attrs_filter' => !empty($product_track_attributes->variables) ? $product_track_attributes->variables : (object)[]
            ]
        );

        return $return;
    }

    public function update_item($cart_id, $item_id, $cart_args, $images_args, $language)
    {
        $product_track_attributes = ProductModel::product_track_attributes();

        $gql = '
            mutation WPCartUpdateItemMutation(
                $cart_id: String!
                $item_id: String!
                $cart_input: CartUpdateItemInput!
                $crossupsells_input: CrossupsellSearchInput
                $img_search: MediaImageProductSearchInput
                $img_transform: TransformationsInput!
                $language: String
                $taxZone: String! = "NL"
                $product_track_attrs_filter: AttributeResultSearchInput
            ){
                cartUpdateItem(id: $cart_id itemId: $item_id input: $cart_input) {
                    ... WPCartDataFragment
                }
            }
        ';

        $queries = [
            self::cart_data_fragment()->query,
            $images_args->query,
            $gql
        ];

        $return = new stdClass();
        $return->query = implode("\n\n", $queries);
        $return->variables = array_merge(
            $images_args->variables,
            [
                'language' => $language,
                'taxZone' => PROPELLER_DEFAULT_TAXZONE,
                'cart_input' => $cart_args,
                'cart_id' => $cart_id,
                'item_id' => $item_id,
                'crossupsells_input' => [
                    'types' => [
                        CrossupsellTypes::ACCESSORIES
                    ]
                ],
                'product_track_attrs_filter' => !empty($product_track_attributes->variables) ? $product_track_attributes->variables : (object)[]
            ]
        );

        return $return;
    }

    public function update_items($cart_args, $images_args, $language)
    {
        $product_track_attributes = ProductModel::product_track_attributes();

        $gql = '
            mutation WPCartUpdateItemsMutation(
                $cartId: String!
                $cartItems: [CartUpdateItemsInput!]!
                $crossupsells_input: CrossupsellSearchInput
                $img_search: MediaImageProductSearchInput
                $img_transform: TransformationsInput!
                $language: String
                $taxZone: String! = "NL"
                $product_track_attrs_filter: AttributeResultSearchInput
            ){
                cartUpdateItems(id: $cartId items: $cartItems) {
                    cart {
                        ... WPCartDataFragment
                    }
                    response {
                        data
                        messages
                    }
                }
            }
        ';

        $queries = [
            self::cart_data_fragment()->query,
            $images_args->query,
            $gql
        ];

        $return = new stdClass();
        $return->query = implode("\n\n", $queries);
        $return->variables = array_merge(
            $images_args->variables,
            [
                'language' => $language,
                'taxZone' => PROPELLER_DEFAULT_TAXZONE,
                'cartId' => $cart_args['cartId'],
                'cartItems' => $cart_args['items'],
                'crossupsells_input' => [
                    'types' => [
                        CrossupsellTypes::ACCESSORIES
                    ]
                ],
                'product_track_attrs_filter' => !empty($product_track_attributes->variables) ? $product_track_attributes->variables : (object)[]
            ]
        );

        return $return;
    }

    public function delete_item($cart_id, $cart_args, $images_args, $language)
    {
        $product_track_attributes = ProductModel::product_track_attributes();

        $gql = '
            mutation WPCartDeleteItem(
                $cart_id: String!
                $cart_input: CartDeleteItemInput!
                $crossupsells_input: CrossupsellSearchInput
                $img_search: MediaImageProductSearchInput
                $img_transform: TransformationsInput!
                $language: String
                $taxZone: String! = "NL"
                $product_track_attrs_filter: AttributeResultSearchInput
            ){
                cartDeleteItem(id: $cart_id input: $cart_input) {
                    ... WPCartDataFragment
                }
            }
        ';

        $queries = [
            self::cart_data_fragment()->query,
            $images_args->query,
            $gql
        ];

        $return = new stdClass();
        $return->query = implode("\n\n", $queries);
        $return->variables = array_merge(
            $images_args->variables,
            [
                'language' => $language,
                'taxZone' => PROPELLER_DEFAULT_TAXZONE,
                'cart_input' => $cart_args,
                'cart_id' => $cart_id,
                'crossupsells_input' => [
                    'types' => [
                        CrossupsellTypes::ACCESSORIES
                    ]
                ],
                'product_track_attrs_filter' => !empty($product_track_attributes->variables) ? $product_track_attributes->variables : (object)[]
            ]
        );

        return $return;
    }

    public function cart_update_address($cart_id, $cart_args, $images_args, $language)
    {
        $product_track_attributes = ProductModel::product_track_attributes();

        $gql = '
            mutation WPCartUpdateAddress(
                $cart_id: String!
                $cart_input: CartUpdateAddressInput!
                $crossupsells_input: CrossupsellSearchInput
                $img_search: MediaImageProductSearchInput
                $img_transform: TransformationsInput!
                $language: String
                $taxZone: String! = "NL"
                $product_track_attrs_filter: AttributeResultSearchInput
            ){
                cartUpdateAddress(id: $cart_id input: $cart_input) {
                    ... WPCartDataFragment
                }
            }
        ';

        $queries = [
            self::cart_data_fragment()->query,
            $images_args->query,
            $gql
        ];

        $return = new stdClass();
        $return->query = implode("\n\n", $queries);
        $return->variables = array_merge(
            $images_args->variables,
            [
                'language' => $language,
                'taxZone' => PROPELLER_DEFAULT_TAXZONE,
                'cart_input' => $cart_args,
                'cart_id' => $cart_id,
                'crossupsells_input' => [
                    'types' => [
                        CrossupsellTypes::ACCESSORIES
                    ]
                ],
                'product_track_attrs_filter' => !empty($product_track_attributes->variables) ? $product_track_attributes->variables : (object)[]
            ]
        );

        return $return;
    }

    public function action_code($cart_id, $cart_args, $images_args, $language)
    {
        $product_track_attributes = ProductModel::product_track_attributes();

        $gql = '
            mutation WPCartAddActionCodeMutation(
                $cart_id: String!
                $cart_input: CartActionCodeInput!
                $crossupsells_input: CrossupsellSearchInput
                $img_search: MediaImageProductSearchInput
                $img_transform: TransformationsInput!
                $language: String
                $taxZone: String! = "NL"
                $product_track_attrs_filter: AttributeResultSearchInput
            ){
                cartAddActionCode(id: $cart_id input: $cart_input) {
                    ... WPCartDataFragment
                }
            }
        ';

        $queries = [
            self::cart_data_fragment()->query,
            $images_args->query,
            $gql
        ];

        $return = new stdClass();
        $return->query = implode("\n\n", $queries);
        $return->variables = array_merge(
            $images_args->variables,
            [
                'language' => $language,
                'taxZone' => PROPELLER_DEFAULT_TAXZONE,
                'cart_id' => $cart_id,
                'cart_input' => $cart_args,
                'crossupsells_input' => [
                    'types' => [
                        CrossupsellTypes::ACCESSORIES
                    ]
                ],
                'product_track_attrs_filter' => !empty($product_track_attributes->variables) ? $product_track_attributes->variables : (object)[]
            ]
        );

        return $return;
    }

    public function remove_action_code($cart_args, $images_args, $language)
    {
        $product_track_attributes = ProductModel::product_track_attributes();

        $gql = '
            mutation WPCartRemoveActionCodeMutation(
                $cartId: String!
                $cart_input: CartActionCodeInput!
                $crossupsells_input: CrossupsellSearchInput
                $img_search: MediaImageProductSearchInput
                $img_transform: TransformationsInput!
                $language: String
                $taxZone: String! = "NL"
                $product_track_attrs_filter: AttributeResultSearchInput
            ){
                cartRemoveActionCode(id: $cartId input: $cart_input) {
                    ... WPCartDataFragment
                }
            }
        ';

        $queries = [
            self::cart_data_fragment()->query,
            $images_args->query,
            $gql
        ];

        $return = new stdClass();
        $return->query = implode("\n\n", $queries);
        $return->variables = array_merge(
            $images_args->variables,
            [
                'language' => $language,
                'taxZone' => PROPELLER_DEFAULT_TAXZONE,
                'cartId' => $cart_args['cartId'],
                'cart_input' => ['actionCode' => $cart_args['actionCode']],
                'crossupsells_input' => [
                    'types' => [
                        CrossupsellTypes::ACCESSORIES
                    ]
                ],
                'product_track_attrs_filter' => !empty($product_track_attributes->variables) ? $product_track_attributes->variables : (object)[]
            ]
        );

        return $return;
    }

    public function voucher_code($cart_args, $images_args, $language)
    {
        $product_track_attributes = ProductModel::product_track_attributes();

        $gql = '
            mutation WPCartAddVoucherCodeMutation(
                $cartId: String!
                $voucherCode: String!
                $crossupsells_input: CrossupsellSearchInput
                $img_search: MediaImageProductSearchInput
                $img_transform: TransformationsInput!
                $language: String
                $taxZone: String! = "NL"
                $product_track_attrs_filter: AttributeResultSearchInput
            ){
                cartAddVoucherCode(cartId: $cartId voucherCode: $voucherCode) {
                    cart {
                        ... WPCartDataFragment
                    }
                    response {
                        data
                        messages
                    }
                }
            }
        ';

        $queries = [
            self::cart_data_fragment()->query,
            $images_args->query,
            $gql
        ];

        $return = new stdClass();
        $return->query = implode("\n\n", $queries);
        $return->variables = array_merge(
            $images_args->variables,
            [
                'language' => $language,
                'taxZone' => PROPELLER_DEFAULT_TAXZONE,
                'cartId' => $cart_args['cartId'],
                'voucherCode' => $cart_args['voucherCode'],
                'crossupsells_input' => [
                    'types' => [
                        CrossupsellTypes::ACCESSORIES
                    ]
                ],
                'product_track_attrs_filter' => !empty($product_track_attributes->variables) ? $product_track_attributes->variables : (object)[]
            ]
        );

        return $return;
    }

    public function remove_voucher_code($cart_args, $images_args, $language)
    {
        $product_track_attributes = ProductModel::product_track_attributes();

        $gql = '
            mutation WPCartRemoveVoucherCodeMutation(
                $cartId: String!
                $voucherCode: String!
                $crossupsells_input: CrossupsellSearchInput
                $img_search: MediaImageProductSearchInput
                $img_transform: TransformationsInput!
                $language: String
                $taxZone: String! = "NL"
                $product_track_attrs_filter: AttributeResultSearchInput
            ){
                cartRemoveVoucherCode(cartId: $cartId voucherCode: $voucherCode) {
                    cart {
                        ... WPCartDataFragment
                    }
                    response {
                        data
                        messages
                    }
                }
            }
        ';

        $queries = [
            self::cart_data_fragment()->query,
            $images_args->query,
            $gql
        ];

        $return = new stdClass();
        $return->query = implode("\n\n", $queries);
        $return->variables = array_merge(
            $images_args->variables,
            [
                'language' => $language,
                'taxZone' => PROPELLER_DEFAULT_TAXZONE,
                'cartId' => $cart_args['cartId'],
                'voucherCode' => $cart_args['voucherCode'],
                'crossupsells_input' => [
                    'types' => [
                        CrossupsellTypes::ACCESSORIES
                    ]
                ],
                'product_track_attrs_filter' => !empty($product_track_attributes->variables) ? $product_track_attributes->variables : (object)[]
            ]
        );

        return $return;
    }

    public function update($cart_id, $cart_args, $images_args, $language)
    {
        $product_track_attributes = ProductModel::product_track_attributes();

        $gql = '
            mutation WPCartUpdateMutation(
                $cart_id: String!
                $cart_input: CartUpdateInput!
                $crossupsells_input: CrossupsellSearchInput
                $img_search: MediaImageProductSearchInput
                $img_transform: TransformationsInput!
                $language: String
                $taxZone: String! = "NL"
                $product_track_attrs_filter: AttributeResultSearchInput
            ){
                cartUpdate(id: $cart_id input: $cart_input) {
                    ... WPCartDataFragment
                }
            }
        ';

        $queries = [
            self::cart_data_fragment()->query,
            $images_args->query,
            $gql
        ];

        $return = new stdClass();
        $return->query = implode("\n\n", $queries);
        $return->variables = array_merge(
            $images_args->variables,
            [
                'language' => $language,
                'taxZone' => PROPELLER_DEFAULT_TAXZONE,
                'cart_input' => $cart_args,
                'cart_id' => $cart_id,
                'crossupsells_input' => [
                    'types' => [
                        CrossupsellTypes::ACCESSORIES
                    ]
                ],
                'product_track_attrs_filter' => !empty($product_track_attributes->variables) ? $product_track_attributes->variables : (object)[]
            ]
        );

        return $return;
    }

    public function process($cart_id, $cart_args, $images_args, $language)
    {
        $product_track_attributes = ProductModel::product_track_attributes();

        $gql = '
            mutation WPCartProcessMutation(
                $cart_id: String!
                $cart_input: CartProcessInput!
                $taxZone: String! = "NL"
                $img_search: MediaImageProductSearchInput
                $img_transform: TransformationsInput!
                $language: String
                $address_type_delivery: AddressType
                $address_type_invoice: AddressType
                $product_track_attrs_filter: AttributeResultSearchInput
            ){
                cartProcess(id: $cart_id input: $cart_input) {
                    cartOrderId
                    order {
                        ... WPOrderDataFragment
                    }
                }
            }
        ';

        $queries = [
            OrderModel::order_data_fragment()->query,
            $images_args->query,
            $gql
        ];

        $return = new stdClass();
        $return->query = implode("\n\n", $queries);
        $return->variables = array_merge(
            $images_args->variables,
            [
                'cart_id' => $cart_id,
                'language' => $language,
                'taxZone' => PROPELLER_DEFAULT_TAXZONE,
                'cart_input' => $cart_args,
                'address_type_delivery' => AddressType::DELIVERY,
                'address_type_invoice' => AddressType::INVOICE,
                'product_track_attrs_filter' => count($product_track_attributes->variables) ? $product_track_attributes->variables : new stdClass(),
            ]
        );

        return $return;
    }

    public static function cart_vouchers_fragment()
    {
        $gql = '
            fragment WPCartVouchersFragment on CartVoucher {
                code
                name
                description
                ruleId
                redeemed
                combinable
                partialRedemption
                available
                remaining
            }
        ';

        $return = new stdClass();
        $return->query = $gql;

        return $return;
    }

    public static function cart_payment_data_fragment()
    {
        $gql = '
            fragment WPCartPaymentDataFragment on CartPaymentData {
                method
                price
                priceNet
                priceMode
                tax
                taxPercentage
                status
                statusDate
            }
        ';

        $return = new stdClass();
        $return->query = $gql;

        return $return;
    }

    public static function cart_postage_data_fragment()
    {
        $gql = '
            fragment WPCartPostageDataFragment on CartPostageData {
                method
                taxPercentage
                requestDate
                price
                priceNet
                partialDeliveryAllowed
                carrier
            }
        ';

        $return = new stdClass();
        $return->query = $gql;

        return $return;
    }

    public static function cart_total_fragment()
    {
        $gql = '
            fragment WPCartTotalFragment on CartTotal {
                subTotal
                subTotalNet
                discountPercentage
                totalNet
                totalGross
                discountNet
                discount
            }
        ';

        $return = new stdClass();
        $return->query = $gql;

        return $return;
    }

    public static function cart_product_fragment()
    {
        $gql = '
            fragment WPCartProductFragment on Product {
                productId
                urlId: productId
                shortNames(language: $language) {
                    value
                    language
                }
                manufacturerCode
                # eanCode
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
                customKeywords {
                    language
                    value
                }
                inventory {
                    totalQuantity
                    supplierQuantity
                    localQuantity
                    balance {
                        quantity
                        warehouseId
                    }
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
            }
        ';

        $return = new stdClass();
        $return->query = $gql;

        return $return;
    }
    public static function cart_product_media_fragment()
    {
        $gql = '
            fragment WPCartProductMediaFragment on Product {
                media {
                    ... WPProductImageFragment
                }
            }
        ';

        $return = new stdClass();
        $return->query = $gql;

        return $return;
    }

    public static function cart_bundle_items_fragment()
    {
        $gql = '
            fragment WPCartBundleItemsFragment on BundleItem {
                isLeader
                price {
                    gross
                    net
                }
                product {
                    class
                    name: names {
                        value
                        language
                    }
                    sku
                    slug: slugs {
                        value
                        language
                    }
                    ... on Product {
                        ... WPCartProductFragment
                        ... WPCartProductMediaFragment
                    }
                }
            }
        ';

        $return = new stdClass();
        $return->query = $gql;

        return $return;
    }

    public static function cart_child_items_fragment()
    {
        $gql = '
            fragment WPCartChildItemsFragment on CartBaseItem {
                itemId
                notes
                price
                priceNet
                totalPrice
                totalPriceNet
                quantity
                bundleId
                productId
                discount
                discountPercentage
                product {
                    class
                    name: names {
                        value
                        language
                    }
                    sku
                    slug: slugs {
                        value
                        language
                    }
                    ... on Product {
                        ... WPCartProductFragment
                        ... WPCartProductMediaFragment
                    }
                }
            }
        ';

        $return = new stdClass();
        $return->query = $gql;

        return $return;
    }

    public static function cart_bundle_fragment()
    {
        $gql = '
            fragment WPCartBundleFragment on Bundle {
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
                    ... WPCartBundleItemsFragment
                }
            }
        ';

        $return = new stdClass();
        $return->query = $gql;

        return $return;
    }

    public static function cart_item_product_fragment()
    {


        $gql = '
            fragment WPCartItemProductFragment on Product {
                class
                urlId: productId
                name: names {
                    value
                    language
                }
                description: descriptions {
                    value
                    language
                }
                customKeywords {
                    language
                    value
                }
                categoryPath {
                    categoryId
                    name(language: $language) {
                        value
                        language
                    }
                }
                sku
                slug: slugs {
                    value
                    language
                }
                ... on Product {
                    ... WPCartProductFragment
                    ... WPCartProductMediaFragment
                }
                crossupsellsFrom(input: $crossupsells_input) {
                    itemsFound                    
                }
                cluster {
                    clusterId
                    urlId: clusterId
                    name: names {
                        value
                        language
                    }
                    slug: slugs {
                        value
                        language
                    }
                }
            }
        ';


        $return = new stdClass();
        $return->query = $gql;

        return $return;
    }
    public static function cart_items_fragment()
    {
        $product_track_attributes = ProductModel::product_track_attributes();



        $gql = '
            fragment WPCartItemsFragment on CartMainItem {
                id: itemId
                notes
                price
                priceNet
                totalPrice
                totalPriceNet
                sum
                sumNet
                totalSum
                totalSumNet
                quantity
                taxCode
                bundleId
                productId
                clusterId
                urlId: productId
                product {
                    ... WPCartItemProductFragment
                }
                childItems {
                    ... WPCartChildItemsFragment
                }
                bundle {
                    ... WPCartBundleFragment                    
                }
                surcharges {
                    ... WPCartItemSurchargesFragment
                }
            }
        ';
        $queries = [
            $gql
        ];

        $return = new stdClass();
        $return->query = implode("\n\n", $queries);
        $return->variables = array_merge(

            [
                'product_track_attrs_filter' => $product_track_attributes->variables
            ]
        );



        return $return;
    }

    public static function cart_item_surcharges_fragment()
    {
        $gql = '
            fragment WPCartItemSurchargesFragment on CartItemSurcharge {
                names {
                    value
                    language
                }
                descriptions {
                    value
                    language
                }
                type
                value
                taxCode
                taxPercentage
                quantity
                price
                totalPrice
                priceNet
                totalPriceNet
            }
        ';

        $return = new stdClass();
        $return->query = $gql;

        return $return;
    }

    public static function cart_bonus_items_fragment()
    {
        $gql = '
            fragment WPCartBonusItemsFragment on CartBaseItem {
                itemId
                quantity
                totalPrice
                totalPriceNet
                product {
                    ... WPCartItemProductFragment
                }
            }
        ';

        $return = new stdClass();
        $return->query = $gql;

        return $return;
    }

    public static function cart_address_fragment()
    {
        $gql = '
            fragment WPCartAddressFragment on CartAddress {
                code
                firstName
                middleName
                lastName
                email
                gender
                country
                city
                street
                number
                numberExtension
                postalCode
                company
                phone
                mobile
                notes
                gender
                icp
            }
        ';

        $return = new stdClass();
        $return->query = $gql;

        return $return;
    }

    public static function cart_tax_levels_fragment()
    {
        $gql = '
            fragment WPCartTaxLevelsFragment on CartTaxLevel {
                taxPercentage
                price
                discount
            }
        ';

        $return = new stdClass();
        $return->query = $gql;

        return $return;
    }

    public static function cart_paymethods_fragment()
    {
        $gql = '
            fragment WPCartPaymethodsFragment on CartPaymethod {
                name
                code
                externalCode
                type
                price
            }
        ';

        $return = new stdClass();
        $return->query = $gql;

        return $return;
    }

    public static function cart_carriers_fragment()
    {
        $gql = '
                fragment WPCartCarriersFragment on CartCarrier {
                    id
                    name
                    logo
                    deliveryDeadline
                }
            ';

        $return = new stdClass();
        $return->query = $gql;

        return $return;
    }

    public function load_item_crossupsells($args, $images_args, $language)
    {
        $product_track_attributes = ProductModel::product_track_attributes();
        $tax_zone = PROPELLER_DEFAULT_TAXZONE;

        $gql = '
            query WPCartItemCrossUpsellsQuery (
                $product_id: Int
                $crossupsells_input: CrossupsellSearchInput
                $img_search: MediaImageProductSearchInput
                $img_transform: TransformationsInput!
                $language: String
                $taxZone: String
                $product_track_attrs_filter: AttributeResultSearchInput
            ) {
                product(productId: $product_id) {
                    crossupsellsFrom(input: $crossupsells_input) {
                        items {
                            id
                            productTo {
                                name: names {
                                    value
                                    language
                                }
                                sku
                                slug: slugs {
                                    value
                                    language
                                }
                                ... on Product {
                                    ... WPCartProductFragment
                                    ... WPCartProductMediaFragment
                                }                 
                            }
                            clusterTo {
                                ...WPCartProductFragment
                                ... on Cluster {
                                    class
                                    clusterId
                                    urlId: clusterId
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
                                    defaultProduct {
                                        productId
                                        urlId: productId
                                        ... WPCartProductMediaFragment
                                        ... WPCartProductFragment
                                    }
                                }
                            }
                        }
                    }
                }
            }
        ';

        $queries = [
            self::cart_product_fragment()->query,
            self::cart_product_media_fragment()->query,
            ProductModel::product_price_data_fragment()->query,
            ProductModel::product_price_fragment()->query,
            self::attributes_fragment()->query,
            $images_args->query,

            $gql
        ];

        $return = new stdClass();
        $return->query = implode("\n\n", $queries);
        $return->variables = array_merge(
            [
                'language' => $language,
                'taxZone' => $tax_zone,
                'price_input' => ProductModel::price_input_array($tax_zone),
                'product_track_attrs_filter' => !empty($product_track_attributes->variables) ? $product_track_attributes->variables : (object)[]
            ],
            $images_args->variables,
            $args
        );

        return $return;
    }

    public function cart_data_fragment()
    {
        $gql = '
            fragment WPCartDataFragment on Cart {
                cartId
                contactId
                companyId
                customerId
                notes
                reference
                extra3
                extra4
                orderStatus
                status
                actionCode
                createdAt
                lastModifiedAt
                language
                purchaseAuthorizationRequired
                vouchers {
                    ... WPCartVouchersFragment
                }
                paymentData {
                    ... WPCartPaymentDataFragment
                }
                postageData {
                    ... WPCartPostageDataFragment
                }
                total {
                    ... WPCartTotalFragment
                }
                items {
                    ... WPCartItemsFragment                
                }
                bonusItems {
                    ... WPCartBonusItemsFragment
                }
                invoiceAddress {
                    ... WPCartAddressFragment
                }
                deliveryAddress {
                    ... WPCartAddressFragment
                }
                taxLevels {
                    ... WPCartTaxLevelsFragment
                }
                payMethods {
                    ... WPCartPaymethodsFragment
                }
                carriers {
                    ... WPCartCarriersFragment
                }            
            }
        ';

        $queries = [
            self::get_cart_data_fragments(),
            $gql
        ];

        $return = new stdClass();
        $return->query = implode("\n\n", $queries);

        return $return;
    }

    private static function get_cart_data_fragments()
    {
        $queries = [
            self::cart_vouchers_fragment()->query,
            self::cart_payment_data_fragment()->query,
            self::cart_postage_data_fragment()->query,
            self::cart_total_fragment()->query,
            self::cart_items_fragment()->query,
            self::cart_item_surcharges_fragment()->query,
            self::cart_item_product_fragment()->query,
            self::cart_bonus_items_fragment()->query,
            self::cart_address_fragment()->query,
            self::cart_tax_levels_fragment()->query,
            self::cart_paymethods_fragment()->query,
            self::cart_carriers_fragment()->query,
            self::cart_child_items_fragment()->query,
            self::cart_bundle_fragment()->query,
            self::cart_bundle_items_fragment()->query,
            self::cart_product_fragment()->query,
            self::cart_product_media_fragment()->query,
            ProductModel::product_price_fragment()->query,
            ProductModel::product_price_data_fragment()->query,
            self::attributes_fragment()->query,
        ];

        return implode("\n\n", $queries);
    }

    public function get_carts($cart_args)
    {
        $gql = '
            query WPCartsQuery(
                $carts_input: CartSearchInput!
            ){
                carts(input: $carts_input) {
                    itemsFound
                    items {
                        cartId
                    }                    
                }
            }
        ';

        $return = new stdClass();
        $return->query = $gql;
        $return->variables = [
            'carts_input' => $cart_args
        ];

        return $return;
    }

    public function get_user_cart($cart_args, $images_args, $language)
    {
        $product_track_attributes = ProductModel::product_track_attributes();

        $gql = '
            query WPGetCartQuery(
                $cartId: String!
                $img_search: MediaImageProductSearchInput
                $img_transform: TransformationsInput!
                $crossupsells_input: CrossupsellSearchInput
                $language: String
                $taxZone: String! = "NL"
                $product_track_attrs_filter: AttributeResultSearchInput
            ){
                cart(id: $cartId) {
                    ... WPCartDataFragment
                }
            }
        ';

        $queries = [
            self::cart_data_fragment()->query,
            $images_args->query,
            $gql
        ];

        if (!defined('PROPELLER_DEFAULT_TAXZONE'))
            UserController::set_default_tax_zone();

        $return = new stdClass();
        $return->query = implode("\n\n", $queries);
        $return->variables = array_merge(
            $cart_args,
            $images_args->variables,
            [
                'language' => $language,
                'taxZone' => PROPELLER_DEFAULT_TAXZONE,
                'crossupsells_input' => [
                    'types' => [
                        CrossupsellTypes::ACCESSORIES
                    ]
                ],
                'product_track_attrs_filter' => !empty($product_track_attributes->variables) ? $product_track_attributes->variables : (object)[]
            ]
        );

        return $return;
    }

    public function delete_cart($cart_args)
    {
        $gql = '
            mutation WPDeleteShoppingCartMutation(
                $cart_id: String!
            ){
                cartDelete(id: $cart_id)
            }        
        ';

        $return = new stdClass();
        $return->query = $gql;
        $return->variables = $cart_args;

        return $return;
    }

    public function check_cart($cart_args)
    {
        $gql = '
            query WPCartCheckQuery(
                $cart_id: String!
            ){
                cart(id: $cart_id) {
                    cartId
                }
            }
        ';

        $return = new stdClass();
        $return->query = $gql;
        $return->variables = [
            'cart_id' => $cart_args
        ];

        return $return;
    }

    public function get_purchase_authorizations($carts_args)
    {
        $gql = '
            query WPPurchaseAuthorizationsQuery(
                $carts_input: CartSearchInput!
            ){
                carts(input: $carts_input) {
                    itemsFound
                    pages
                    page
                    offset
                    items {
                        cartId
                        contactId
                        companyId
                        contact {
                            firstName
                            middleName
                            lastName
                        }
                        items {
                            itemId
                        }
                        total {
                            subTotal
                            subTotalNet
                            totalGross
                            totalNet
                        }
                        lastModifiedAt
                    }
                }
            }
        ';

        $return = new stdClass();
        $return->query = $gql;
        $return->variables = [
            'carts_input' => $carts_args
        ];

        return $return;
    }

    public function accept_purchase_authorization_request($authorization_args, $images_args, $language)
    {
        $product_track_attributes = ProductModel::product_track_attributes();

        $gql = '
            mutation WPAcceptPurchaseAuthorizationMutation(
                $cart_id: String!
                $authorizer_input: CartAcceptPurchaseAuthorizationRequestInput
                $crossupsells_input: CrossupsellSearchInput
                $img_search: MediaImageProductSearchInput
                $img_transform: TransformationsInput!
                $language: String
                $taxZone: String! = "NL"
                $product_track_attrs_filter: AttributeResultSearchInput
            ){
                cartAcceptPurchaseAuthorizationRequest(id: $cart_id input: $authorizer_input) {
                    ... WPCartDataFragment
                }
            }
        ';

        $queries = [
            self::cart_data_fragment()->query,
            $images_args->query,
            $gql
        ];

        $return = new stdClass();
        $return->query = implode("\n\n", $queries);
        $return->variables = array_merge(
            $images_args->variables,
            [
                'language' => $language,
                'taxZone' => PROPELLER_DEFAULT_TAXZONE,
                'authorizer_input' => $authorization_args['input'],
                'cart_id' => $authorization_args['cart_id'],
                'crossupsells_input' => [
                    'types' => [
                        CrossupsellTypes::ACCESSORIES
                    ]
                ],
                'product_track_attrs_filter' => !empty($product_track_attributes->variables) ? $product_track_attributes->variables : (object)[]
            ]
        );

        return $return;
    }

    public function submit_purchase_request($request_args)
    {
        $gql = '
            mutation WPSubmitPurchaseAuthorizationMutation(
                $cart_id: String!
            ){
                cartRequestPurchaseAuthorization(id: $cart_id) {
                    cartId
                }
            }
        ';


        $return = new stdClass();
        $return->query = $gql;
        $return->variables = [
            'cart_id' => $request_args['cart_id']
        ];

        return $return;
    }

    public function get_paymethods($params)
    {
        $gql = '
            query WPPaymethodsQuery(
                $payments_params: PayMethodSearchInput
            ){
                payMethods(input: $payments_params){
                    itemsFound
                    items {
                        id
                        names {
                            language
                            value
                        }
                        externalCode
                        logo      
                    }
                }
            }
        ';

        $return = new stdClass();
        $return->query = $gql;
        $return->variables = [
            'payments_params' => $params
        ];

        return $return;
    }

    public function get_carriers_temp($offset)
    {
        $gql = '
            query CarrierQuery(
                $carriers_input: CarriersSearchInput
            ){                
                carriers (input: $carriers_input) {
                    items {
                        id
                        name
                        type
                        descriptions {
                            language
                            value
                        }
                        trackAndTraceURL
                        logo                    
                    }
                }
            }
        ';

        $return = new stdClass();
        $return->query = $gql;
        $return->variables = [
            'carriers_input' => [
                "offset" => $offset
            ]
        ];

        return $return;
    }
}
