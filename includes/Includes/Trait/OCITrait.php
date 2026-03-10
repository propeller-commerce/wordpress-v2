<?php

namespace Propeller\Includes\Trait;

if ( ! defined( 'ABSPATH' ) ) exit;

use Propeller\Includes\Controller\SessionController;
use Propeller\Includes\Object\Product;
use Propeller\PropellerHelper;

trait OCITrait
{
    public static function getUrlParamsMap() {
        return [
            '~CALLER',
            '~OKCODE',
            '~TARGET',
            'VENDOR',
            'CLASSIFICATION',
            'DEFAULTUNIT',
            'CUSTFIELD1',
            'CUSTFIELD2',
            'CUSTFIELD3',
            'CUSTFIELD4',
            'CUSTFIELD5',
        ];
    }

    public static function mapUrlParamsForSession($url_params) {
        $mapped = [];

        foreach ($url_params as $name => $value) {
            if (!in_array($name, self::getUrlParamsMap()))
                continue;

            $mapped_name = $name; 

            if (str_contains($mapped_name, '~'))
                $mapped_name = str_replace('~', '', $mapped_name);

            
            $mapped[$mapped_name] = $value;
        }

        return $mapped;
    }

    public static function convertCartToOCI($cart, $actionUrl, $submitLabel = 'Send via punchout') {
        $fields = [];

        // Add general cart metadata if needed
        $fields['~OkCode'] = SessionController::has('OKCODE') ? SessionController::get('OKCODE') : ''; // Optional SAP-specific field
        $fields['~caller'] = SessionController::has('CALLER') ? SessionController::get('CALLER') : '';
        $fields['~target'] = SessionController::has('TARGET') ? SessionController::get('TARGET') : '';

        $i = 1;

        foreach ($cart->items as $item) {
            $product = new Product($item->product);

            
            $customFieldValue_1 = $item->totalSumNet;
            $customFieldValue_2 = $item->taxCode;
            $customFieldValue_3 = $item->totalSum > 0 && $item->totalSumNet > 0 ? PropellerHelper::percentage_from_values($item->totalSum, $item->totalSumNet) : 0;
            $customFieldValue_4 = '';
            $customFieldValue_5 = '';

            if (SessionController::has('CUSTFIELD1')) {
                switch (SessionController::get('CUSTFIELD1')) {
                    case 'taxpercentage': 
                        $customFieldValue_1 = $item->totalSum > 0 && $item->totalSumNet > 0 ? PropellerHelper::percentage_from_values($item->totalSum, $item->totalSumNet) : 0;
                        $customFieldValue_3 = '';
                        break;
                    default: 
                        $customFieldValue_1 = $item->totalSumNet;
                        $customFieldValue_3 = $item->totalSum > 0 && $item->totalSumNet > 0 ? PropellerHelper::percentage_from_values($item->totalSum, $item->totalSumNet) : 0;
                        break;
                }
            }

            if (SessionController::has('CUSTFIELD2')) {
                switch (SessionController::get('CUSTFIELD2')) {
                    case 'deliveryDate': 
                        $customFieldValue_2 = $item->deliveryDate;
                        break;
                    default: 
                        $customFieldValue_2 =  $item->taxCode;
                        break;
                }
            }

            // Core item fields
            $fields["NEW_ITEM-DESCRIPTION[$i]"] = $product->get_name();
            $fields["NEW_ITEM-QUANTITY[$i]"] = $item->quantity;
            $fields["NEW_ITEM-PRICE[$i]"] = number_format($item->price, 2, '.', '');
            $fields["NEW_ITEM-CURRENCY[$i]"] = 'EUR';
            $fields["NEW_ITEM-UNIT[$i]"] = self::normalizeUnit($product->package, $product->packageUnit) ?? 'EA';
            $fields["NEW_ITEM-VENDORMAT[$i]"] = $product->sku ?? '';
            $fields["NEW_ITEM-MANUFACTMAT[$i]"] = $product->manufacturer ?? '';
            $fields["NEW_ITEM-MANUFACTCODE[$i]"] = $product->manufacturerCode ?? '';
            $fields["NEW_ITEM-EXT_PRODUCT_ID[$i]"] = $product->productId ?? '';
            $fields["NEW_ITEM-LONGTEXT[$i]:132"] = isset($product->description[0]->value) ? $product->description[0]->value : '';

            // Optional OCI fields (from your screenshot)
            // $fields["NEW_ITEM-MATNR[$i]"] = $product->productId ?? '';
            $fields["NEW_ITEM-MATNR[$i]"] = '';
            $fields["NEW_ITEM-MATGROUP[$i]"] = SessionController::has('CLASSIFICATION') ? SessionController::get('CLASSIFICATION') : '';
            $fields["NEW_ITEM-PRICEUNIT[$i]"] = $product->packageUnitQuantity ?? '1';
            $fields["NEW_ITEM-CONTRACT[$i]"] = '';
            $fields["NEW_ITEM-LEADTIME[$i]"] = '';
            $fields["NEW_ITEM-SERVICE[$i]"] = '';
            $fields["NEW_ITEM-SERVICE_ITEM[$i]"] = '';
            $fields["NEW_ITEM-EXT_QUOTE_ID[$i]"] = '';
            $fields["NEW_ITEM-CUST_FIELD1[$i]"] = $customFieldValue_1;
            $fields["NEW_ITEM-CUST_FIELD2[$i]"] = $customFieldValue_2;
            $fields["NEW_ITEM-CUST_FIELD3[$i]"] = $customFieldValue_3;
            $fields["NEW_ITEM-CUST_FIELD4[$i]"] = $customFieldValue_4;
            $fields["NEW_ITEM-CUST_FIELD5[$i]"] = $customFieldValue_5;
            $fields["NEW_ITEM-ATTACHMENT[$i]"] = $product->has_images() ? $product->images[0]->images[0]->url : '';
            $fields["NEW_ITEM-ATTACHMENT_TITLE[$i]"] = '';
            $fields["NEW_ITEM-ATTACHMENT_PURPOSE[$i]"] = '';

            $i++;
        }

        // Shipping costs item
        $fields["NEW_ITEM-DESCRIPTION[$i]"] = esc_html(__('Shipping costs', 'propeller-ecommerce-v2'));
        $fields["NEW_ITEM-QUANTITY[$i]"] = 1;
        $fields["NEW_ITEM-PRICE[$i]"] = number_format($cart->postageData->price, 2, '.', '');
        $fields["NEW_ITEM-CURRENCY[$i]"] = 'EUR';
        $fields["NEW_ITEM-UNIT[$i]"] = self::normalizeUnit('stuk') ?? 'PCE';
        $fields["NEW_ITEM-VENDORMAT[$i]"] = esc_html(__('Shipping costs', 'propeller-ecommerce-v2'));
        $fields["NEW_ITEM-MANUFACTMAT[$i]"] = '';
        $fields["NEW_ITEM-MANUFACTCODE[$i]"] = '';
        $fields["NEW_ITEM-EXT_PRODUCT_ID[$i]"] = '';
        $fields["NEW_ITEM-LONGTEXT[$i]:132"] = '';
        // $fields["NEW_ITEM-MATNR[$i]"] = $product->productId ?? '';
        $fields["NEW_ITEM-MATNR[$i]"] = '';
        $fields["NEW_ITEM-MATGROUP[$i]"] = '';
        $fields["NEW_ITEM-PRICEUNIT[$i]"] = '';
        $fields["NEW_ITEM-CONTRACT[$i]"] = '';
        $fields["NEW_ITEM-LEADTIME[$i]"] = '';
        $fields["NEW_ITEM-SERVICE[$i]"] = '';
        $fields["NEW_ITEM-SERVICE_ITEM[$i]"] = '';
        $fields["NEW_ITEM-EXT_QUOTE_ID[$i]"] = '';
        $fields["NEW_ITEM-CUST_FIELD1[$i]"] = number_format($cart->postageData->priceNet, 2, '.', '');
        $fields["NEW_ITEM-CUST_FIELD2[$i]"] = $cart->items[0]->taxCode;
        $fields["NEW_ITEM-CUST_FIELD3[$i]"] = $cart->postageData->price > 0 && $cart->postageData->priceNet > 0 ? PropellerHelper::percentage_from_values($cart->postageData->price, $cart->postageData->priceNet) : 0;
        $fields["NEW_ITEM-CUST_FIELD4[$i]"] = '';
        $fields["NEW_ITEM-CUST_FIELD5[$i]"] = '';
        $fields["NEW_ITEM-ATTACHMENT[$i]"] = '';
        $fields["NEW_ITEM-ATTACHMENT_TITLE[$i]"] = '';
        $fields["NEW_ITEM-ATTACHMENT_PURPOSE[$i]"] = '';

        // Start building form
        $form = "<form method=\"POST\" action=\"" . htmlspecialchars($actionUrl) . "\" name=\"ociform\" id=\"ociForm\" class=\"propeller-oci-form-btn\" data-cart=\"" . $cart->cartId . "\">\n";

        foreach ($fields as $name => $value) {
            $escapedName = htmlspecialchars($name);
            $escapedValue = htmlspecialchars($value ?? '');
            $form .= "<input type=\"hidden\" name=\"$escapedName\" value=\"$escapedValue\" />\n";
        }

        $form .= "<input type=\"button\" class=\"btn btn-checkout\" value=\"" . htmlspecialchars($submitLabel) . "\" />\n";
        $form .= "</form>\n";

        return $form;
    }

    public static function normalizeUnit($unit, $defaultUnit = '') {
        $normalizedUnit = $unit;

        if (!empty($defaultUnit)) {
            $normalizedUnit = $defaultUnit;
        } elseif (stripos($unit, 'doos') !== false) {
            $normalizedUnit = 'CS';
        } elseif (stripos($unit, 'stuk') !== false) {
            $normalizedUnit = 'PCE';
        } elseif (stripos($unit, 'etui') !== false) {
            $normalizedUnit = 'ET';
        } elseif (stripos($unit, 'pak') !== false) {
            $normalizedUnit = 'PK';
        } elseif (stripos($unit, 'rol') !== false) {
            $normalizedUnit = 'RO';
        } elseif (stripos($unit, 'set') !== false) {
            $normalizedUnit = 'SET';
        } elseif (stripos($unit, 'blis') !== false) {
            $normalizedUnit = 'BR';
        } elseif (stripos($unit, 'disp') !== false) {
            $normalizedUnit = 'DP';
        } elseif (stripos($unit, 'flac') !== false) {
            $normalizedUnit = 'FC';
        } elseif (stripos($unit, 'zak') !== false) {
            $normalizedUnit = 'PCE';
        } elseif (stripos($unit, 'pak') !== false) {
            $normalizedUnit = 'PA';
        } elseif (stripos($unit, 'koke') !== false) {
            $normalizedUnit = 'KK';
        } elseif (stripos($unit, 'fles') !== false) {
            $normalizedUnit = 'FL';
        } elseif (stripos($unit, 'blik') !== false) {
            $normalizedUnit = 'BL';
        } elseif (stripos($unit, 'spin') !== false) {
            $normalizedUnit = 'PK';
        } elseif (stripos($unit, 'emmer') !== false) {
            $normalizedUnit = 'EM';
        } elseif (stripos($unit, 'krimp') !== false) {
            $normalizedUnit = 'PK';
        } elseif (stripos($unit, 'ompak') !== false) {
            $normalizedUnit = 'PK';
        } else {
            $normalizedUnit = 'PCE';
        }

        return $normalizedUnit;
    }
}