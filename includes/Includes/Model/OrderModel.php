<?php

namespace Propeller\Includes\Model;

if ( ! defined( 'ABSPATH' ) ) exit;

use Propeller\Includes\Controller\UserController;
use Propeller\Includes\Enum\AddressType;
use stdClass;

class OrderModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_orders($orders_args)
    {
        $gql = '
            query WPGetOrdersQuery(
                $orders_args: OrderSearchArguments!
            ){
                orders(input: $orders_args) {
                    start
                    end
                    itemsFound
                    offset
                    page
                    pages
                    items {
                        ... WPOrderFragment
                    }
                }
            }        
        ';

        $queries = [
            self::order_list_fragment()->query,
            $gql
        ];

        $return = new stdClass();
        $return->query = implode("\n\n", $queries);
        $return->variables = [
            'orders_args' => $orders_args
        ];

        return $return;
    }

    public static function order_list_fragment()
    {
        $gql = '
            fragment WPOrderFragment on Order {
                id
                status
                createdAt
                validUntil
                statusDate
                public
                invalid
                invalidationReason
                total {
                    ... WPOrderTotalsFragment
                }  
                items {
                    id
                    quantity
                    class
                    parentOrderItemId
                    isBonus
                }
            }
        ';

        $queries = [
            self::order_totals_fragment()->query,
            $gql
        ];

        $return = new stdClass();
        $return->query = implode("\n\n", $queries);
        $return->variables = [];

        return $return;
    }

    public function get_order($order_id, $images_args, $language)
    {
        if (!defined('PROPELLER_DEFAULT_TAXZONE'))
            UserController::set_default_tax_zone();

        $product_track_attributes = self::product_track_attributes();

        $gql = '
            query WPOrderQuery(
                $order_id: Int
                $language: String
                $taxZone: String! = "NL"
                $img_search: MediaImageProductSearchInput
                $img_transform: TransformationsInput!
                $product_track_attrs_filter: AttributeResultSearchInput!
                $address_type_delivery: AddressType
                $address_type_invoice: AddressType
            ){
                order(orderId: $order_id) {
                    ... WPOrderDataFragment
                }
            }        
        ';

        $queries = [
            $images_args->query,
            self::order_data_fragment()->query,
            // self::order_totals_fragment()->query,
            $gql
        ];

        $return = new stdClass();
        $return->query = implode("\n\n", $queries);
        $return->variables = array_merge(
            [
                'order_id' => $order_id,
                'language' => $language,
                'taxZone' => PROPELLER_DEFAULT_TAXZONE,
                'product_track_attrs_filter' => count($product_track_attributes->variables) ? $product_track_attributes->variables : new stdClass(),
                'address_type_delivery' => AddressType::DELIVERY,
                'address_type_invoice' => AddressType::INVOICE,
            ],
            $images_args->variables
        );

        return $return;
    }

    public function get_shipment_details($shipment_id, $images_args, $language)
    {
        $gql = '
            query WPShipmentQuery(
                $shipment_id: String!
                $language: String
                $img_search: MediaImageProductSearchInput
                $img_transform: TransformationsInput!
            ){
                shipment(id: $shipment_id) {
                    ... WPOrderShipmentFragment
                }
            }        
        ';

        $queries = [
            $images_args->query,
            self::order_shipment_fragment()->query,
            $gql
        ];

        $return = new stdClass();
        $return->query = implode("\n\n", $queries);
        $return->variables = array_merge(
            [
                'shipment_id' => $shipment_id,
                'language' => $language,
            ],
            $images_args->variables
        );

        return $return;
    }

    public function get_order_minimal($order_args, $images_args, $language)
    {
        $product_track_attributes = self::product_track_attributes();

        $gql = '
            query WPOrderMinimalQuery(
                $order_id: Int
                $taxZone: String! = "NL"
                $img_search: MediaImageProductSearchInput
                $img_transform: TransformationsInput!
                $language: String
                $address_type_delivery: AddressType
                $address_type_invoice: AddressType
                $product_track_attrs_filter: AttributeResultSearchInput!
            ){
                order(orderId: $order_id) {
                    ... WPOrderDataFragment                     
                }
            }        
        ';

        $queries = [
            self::order_data_fragment()->query,
            $images_args->query,
            $gql
        ];

        $return = new stdClass();
        $return->query = implode("\n\n", $queries);
        $return->variables = array_merge(
            $images_args->variables,
            [
                'language' => $language,
                'order_id' => $order_args,
                'taxZone' => PROPELLER_DEFAULT_TAXZONE,
                'address_type_delivery' => AddressType::DELIVERY,
                'address_type_invoice' => AddressType::INVOICE,
                'product_track_attrs_filter' => count($product_track_attributes->variables) ? $product_track_attributes->variables : new stdClass(),
            ]
        );

        return $return;
    }

    public function get_invoices($orders_args, $invoices_args, $language)
    {
        $gql = '
            query WPOrderInvoicesQuery(
                $orders_args: OrderSearchArguments!
                $invoices_args: ObjectMediaSearchInput
            ){
                orders(input: $orders_args) {
                    start
                    end
                    itemsFound
                    offset
                    page
                    pages
                    items {
                        ... WPOrderInvoicesFragment
                    }
                }
            }        
        ';

        $queries = [
            self::order_invoices_fragment()->query,
            $gql
        ];

        $return = new stdClass();
        $return->query = implode("\n\n", $queries);
        $return->variables = [
            'orders_args' => $orders_args,
            'invoices_args' => $invoices_args,
            'language' => $language
        ];

        return $return;
    }

    public static function order_invoices_fragment()
    {
        $gql = '
            fragment WPOrderInvoicesFragment on Order {
                id
                status
                date
                validUntil
                statusDate
                paymentData {
                    status   
                }
                total {
                    ... WPOrderTotalsFragment
                }  
                media {
                    attachments(input: $invoices_args) {
                        ... WPOrderMediaAttachmentsFragment
                    }
                }
            }
        ';

        $queries = [
            self::order_totals_fragment()->query,
            self::order_media_attachments_fragment()->query,
            $gql
        ];

        $return = new stdClass();
        $return->query = implode("\n\n", $queries);
        $return->variables = [];

        return $return;
    }

    public function get_pdf($order_args)
    {
        $gql = '
            query WPOrderPdf(
                $orderId: Int!
            ){
                orderGetPDF(orderId: $orderId) {
                    base64
                    contentType
                    fileName
                }
            }
        ';

        $return = new stdClass();
        $return->query = $gql;
        $return->variables = $order_args;

        return $return;
    }

    public function get_pdf_order_user_id($order_args)
    {
        $gql = '
            query WPOrderUserIdPdfQuery(
                $orderId: Int
            ){
                order(orderId: $orderId) {
                    userId
                }
            }
        ';

        $return = new stdClass();
        $return->query = $gql;
        $return->variables = $order_args;

        return $return;
    }

    public function order_cofirm_email($arguments)
    {
        $str_args = $this->parse_arguments($arguments);

        $gql = "
            query {
                orderSendConfirmationEmail($str_args) {
                    messageId
                    success
                }
            }
        ";

        return $gql;
    }

    public function change_status($order_args, $images_args, $language)
    {
        $product_track_attributes = self::product_track_attributes();

        $gql = '
            mutation WPOrderSetStatusMutation(
                $order_input: OrderSetStatusInput!
                $img_search: MediaImageProductSearchInput
                $img_transform: TransformationsInput!
                $language: String
                $taxZone: String! = "NL"
                $address_type_delivery: AddressType
                $address_type_invoice: AddressType
                $product_track_attrs_filter: AttributeResultSearchInput!
            ){
                orderSetStatus(input: $order_input) {
                    ... WPOrderDataFragment
                }
            }
        ';

        $queries = [
            self::order_data_fragment()->query,
            $images_args->query,
            $gql
        ];

        $return = new stdClass();
        $return->query = implode("\n\n", $queries);
        $return->variables = array_merge(
            $images_args->variables,
            [
                'language' => $language,
                'order_input' => $order_args,
                'taxZone' => PROPELLER_DEFAULT_TAXZONE,
                'address_type_delivery' => AddressType::DELIVERY,
                'address_type_invoice' => AddressType::INVOICE,
                'product_track_attrs_filter' => count($product_track_attributes->variables) ? $product_track_attributes->variables : new stdClass(),
            ]
        );

        return $return;
    }

    public static function order_data_fragment()
    {
        $gql = '
            fragment WPOrderDataFragment on Order {
                cartId
                createdAt
                currency
                currencyRatio
                createdAt
                email
                emailDate
                externalId
                id
                reference
                remarks
                public
                revisionNumber
                publicVersionNumber
                invalid
                invalidationReason
                shipments {
                    ... WPOrderShipmentsFragment
                }
                source
                status
                statusDate
                validUntil
                total {
                    ... WPOrderTotalsFragment
                }
                type
                userId
                companyId
                uuid
                accountManagerId
                language
                deliveryAddress: addresses(type: $address_type_delivery) {
                    ... WPAddressFragment
                }
                invoiceAddress: addresses(type: $address_type_invoice) {
                    ... WPAddressFragment
                }
                items {
                    ... WPOrderItemFragment
                }
                paymentData {
                    ... WPOrderPaymentDataFragment
                }
                postageData {
                    ... WPOrderPostageDataFragment
                }
                media {
                    attachments {
                        ... WPOrderMediaAttachmentsFragment
                    }
                }
            }
        ';

        $queries = [
            AddressModel::address_fragment()->query,
            ProductModel::product_order_fragment()->query,
            self::order_shipments_fragment()->query,
            self::order_track_and_trace_fragment()->query,
            self::order_shipment_items_fragment()->query,
            self::order_payment_data_fragment()->query,
            self::order_postage_data_fragment()->query,
            self::order_totals_fragment()->query,
            self::order_item_fragment()->query,
            self::order_media_attachments_fragment()->query,

            $gql
        ];

        $return = new stdClass();
        $return->query = implode("\n\n", $queries);

        return $return;
    }

    public static function order_item_fragment()
    {
        $gql = '
            fragment WPOrderItemFragment on OrderItem {
                id
                orderId
                productId
                class
                name
                originalPrice
                price
                priceTotal
                priceTotalNet
                customerPrice
                costPrice
                discount
                tax
                taxCode
                taxPercentage
                quantity
                sku
                supplier
                supplierCode
                manufacturer
                manufacturerCode
                isBonus
                notes
                parentOrderItemId
                product {
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
                    ... WPProductOrderFragment
                    cluster {
                        id
                        clusterId
                        urlId: clusterId
                        slug: slugs(language: $language) {
                            value
                            language
                        }
                    }
                }
            }
        ';

        $return = new stdClass();
        $return->query = $gql;

        return $return;
    }

    public static function order_totals_fragment()
    {
        $gql = '
            fragment WPOrderTotalsFragment on OrderTotals {
                gross
                net
                tax
                discountType
                discountValue
                taxPercentages {
                    percentage
                    total
                }
            }
        ';

        $return = new stdClass();
        $return->query = $gql;

        return $return;
    }

    public static function order_shipments_fragment()
    {
        $gql = '
            fragment WPOrderShipmentsFragment on Shipment {
                createdAt
                lastModifiedAt
                id
                orderId
                status
                items {
                    ...WPOrderShipmentItemsFragment
                }
                trackAndTraces {
                    ...WPOrderTrackAndTraceFragment
                }
            }
        ';

        $return = new stdClass();
        $return->query = $gql;

        return $return;
    }

    public static function order_shipment_items_fragment()
    {
        $gql = '
            fragment WPOrderShipmentItemsFragment on ShipmentItem {
                createdAt
                id
                name
                orderItemId
                quantity
                shipmentId
                sku  
            }
        ';

        $return = new stdClass();
        $return->query = $gql;

        return $return;
    }

    public function order_shipment_fragment()
    {
        $gql = '
            fragment WPOrderShipmentFragment on Shipment {
                createdAt
                lastModifiedAt
                id
                orderId
                status
                items {
                    ...WPOrderShipmentItemsWithProductFragment
                }
                trackAndTraces {
                    ...WPOrderTrackAndTraceFragment
                }
            }
        ';

        $queries = [
            self::order_shipment_items_with_product_fragment()->query,
            self::order_track_and_trace_fragment()->query,

            $gql
        ];

        $return = new stdClass();
        $return->query = implode("\r\n", $queries);
        $return->variables = [];

        return $return;
    }

    public static function order_shipment_items_with_product_fragment()
    {
        $gql = '
            fragment WPOrderShipmentItemsWithProductFragment on ShipmentItem {
                createdAt
                id
                name
                orderItemId
                quantity
                shipmentId
                sku  
                orderItem {
                    product {
                        ... WPProductShipmentFragment
                    }
                } 
            }
        ';

        $return = new stdClass();
        $return->query = implode("\r\n", [$gql, ProductModel::product_shipment_fragment()->query]);

        return $return;
    }

    public static function order_track_and_trace_fragment()
    {
        $gql = '
            fragment WPOrderTrackAndTraceFragment on TrackAndTrace {
                carrierId
                code
                id
                shipmentId
            }
        ';

        $return = new stdClass();
        $return->query = $gql;

        return $return;
    }

    public function order_shipment_track_and_trace($args)
    {
        $gql = '
            query WPCarriersTrackAndTraceQuery(
                $carriers_input: CarriersSearchInput
            ) {
                carriers(input: $carriers_input) {
                    itemsFound
                    items {
                        id
                        name
                        trackAndTraceURL
                        logo
                    }
                }
            }
        ';

        $return = new stdClass();
        $return->query = $gql;
        $return->variables = $args;
        return $return;
    }

    public static function order_payment_data_fragment()
    {
        $gql = '
            fragment WPOrderPostageDataFragment on OrderPostageData {
                method
                gross
                net
                pickUpLocationId
                partialDeliveryAllowed
                requestDate
                tax
                taxPercentage
                carrier
            }
        ';

        $return = new stdClass();
        $return->query = $gql;

        return $return;
    }

    public static function order_postage_data_fragment()
    {
        $gql = '
            fragment WPOrderPaymentDataFragment on OrderPaymentData {
                gross
                method
                net
                status
                statusDate
                tax
                taxPercentage
            }
        ';

        $return = new stdClass();
        $return->query = $gql;

        return $return;
    }

    public static function order_media_attachments_fragment()
    {
        $gql = '
            fragment WPOrderMediaAttachmentsFragment on PaginatedMediaAttachmentResponse {
                itemsFound
                items {
                    id
                    type
                    sparePartsMachineId
                    companyId
                    customerId
                    orderId
                    priority
                    createdAt
                    tags {
                        language
                        values
                    }
                    alt {
                        language
                        value
                    }
                    attachments {
                        language
                        mimeType
                        originalUrl
                    }
                }
            }
        ';

        $return = new stdClass();
        $return->query = $gql;

        return $return;
    }

    public static function get_order_attachment_query($attachment_id)
    {
        $gql = '
            query WPOrderMediaAttachment(
                $attachment_id: String!
            ) {
                media {
                    attachment(id: $attachment_id) {
                        id
                        sparePartsMachineId
                        alt {
                            language
                            value
                        }
                        description {
                            language
                            value
                        }
                        tags {
                            language
                            values
                        }
                        type
                        createdAt
                        lastModifiedAt
                        priority
                        attachments {
                            language
                            originalUrl
                            mimeType
                        }
                        orderId
                        companyId
                        customerId
                        } 
                    }
                }
        ';

        $return = new stdClass();
        $return->query = $gql;
        $return->variables = [
            "attachment_id" => $attachment_id
        ];

        return $return;
    }

    public function send_order_confirm($order_args)
    {
        $gql = "
            mutation WPTriggerSendOrderconfirm  (
                \$order_confirm_input: TriggerOrderSendConfirmEventInput!
            ) {
                triggerOrderSendConfirm(input: \$order_confirm_input)
            }       
        ";

        $return = new stdClass();
        $return->query = $gql;
        $return->variables = [
            'order_confirm_input' => $order_args
        ];

        return $return;
    }

    public function send_request_confirm($request_args)
    {
        $gql = "
            mutation WPTriggerQuoteSendRequest  (
                \$request_confirm_input: TriggerQuoteSendRequestEventInput!
            ) {
                triggerQuoteSendRequest(input: \$request_confirm_input)
            }       
        ";

        $return = new stdClass();
        $return->query = $gql;
        $return->variables = [
            'request_confirm_input' => $request_args
        ];

        return $return;
    }
}
