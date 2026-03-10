<?php

namespace Propeller\Includes\Trait;

if ( ! defined( 'ABSPATH' ) ) exit;

use DOMDocument;
use DOMElement;
use Exception;
use Propeller\Includes\Controller\ProductController;
use Propeller\Includes\Controller\SessionController;
use Propeller\Includes\Controller\UserController;
use Propeller\Includes\Object\Product;
use Propeller\Includes\Object\User;
use Propeller\PropellerHelper;
use stdClass;

trait CxmlTrait
{
    /**
     * Handle a cXML PunchOutSetupRequest and return the response XML.
     *
     * @param string $rawXml The raw XML POST body sent by the buyer system.
     * @param User[] $users All contacts for which a CXML punchout can be initiated.
     * @return string cXML PunchOutSetupResponse
     */
    public function handlePunchOutSetupRequest(string $rawXml, array $users)
    {
        // Parse the incoming XML
        $xml = $this->parseCxml($rawXml);

        if (!$xml) {
            return $this->buildErrorResponse('400', 'Invalid XML payload');
        }

        $from = (string) $xml->Header->From->Credential->Identity;
        $to = (string) $xml->Header->To->Credential->Identity;
        $senderIdentity = (string) $xml->Header->Sender->Credential->Identity;
        $sharedSecret   = (string) $xml->Header->Sender->Credential->SharedSecret;
        $buyerCookie    = (string) $xml->Request->PunchOutSetupRequest->BuyerCookie;
        $returnUrl      = (string) $xml->Request->PunchOutSetupRequest->BrowserFormPost->URL;
        // $contactName    = (string) $xml->Request->PunchOutSetupRequest->Contact->Name;
        // $contactEmail   = (string) $xml->Request->PunchOutSetupRequest->Contact->Email;

        // Find the user matching the shared secret by validating sender credentials
        $user = null;
        foreach ($users as $u) {
            if ($this->validatePunchoutCredentials($senderIdentity, $sharedSecret, $u)) {
                $user = $u;
                break;
            }
        }

        // if no valid user found, return error response
        if (!$user)
            return $this->buildErrorResponse('401', 'Unauthorized');

        $sessionId = uniqid('punchout_', true);
        $timestamp = gmdate('Y-m-d\TH:i:sP');

        $startUrl  = $this->getPunchoutStartUrl($sessionId, $buyerCookie, $returnUrl, $user, $from, $to);
        // $escapedUrl = htmlspecialchars($startUrl, ENT_QUOTES | ENT_XML1, 'UTF-8');
        
        // Build PunchOutSetupResponse
        $response = '<?xml version="1.0"?>' . "\n" .
            '<!DOCTYPE cXML SYSTEM "http://xml.cxml.org/schemas/cXML/1.2.019/cXML.dtd">' . "\n" .
            '<cXML payloadID="' . esc_attr($sessionId) . '" timestamp="' . esc_attr($timestamp) . '">' . "\n" .
            '    <Response>' . "\n" .
            '        <Status code="200" text="OK"/>' . "\n" .
            '        <PunchOutSetupResponse>' . "\n" .
            '            <StartPage>' . "\n" .
            '                <URL><![CDATA[' . esc_url($startUrl) . ']]></URL>' . "\n" .
            '            </StartPage>' . "\n" .
            '        </PunchOutSetupResponse>' . "\n" .
            '    </Response>' . "\n" .
            '</cXML>';

        $data = new stdClass();
        $data->sessionId = $sessionId;
        $data->timestamp = $timestamp;
        $data->startUrl  = $startUrl;

        // log request + response
        $this->logPunchout('SetupRequest', $rawXml);
        $this->logPunchout('SetupResponse', $response);

        return $data;
    }

    /**
     * Build the cXML PunchOut Order Message.
     *
     * @param Object $cart Shopping cart object
     * @param string $buyerCookie Optional buyer cookie value.
     * @param string $deploymentMode "production" or "test"
     * @return string The generated cXML string.
     */
    public function buildOrderCxml(object $cart, string $buyerCookie = '', string $deploymentMode = 'test', string $uom_attr = '', array $classification = []): string
    {
        $exchangeRate = 1.0;

        // Example currency conversion logic
        // if (isset($cart['currency']) && strtoupper($cart['currency']) !== 'EUR') {
        //     $exchangeRate = $this->getExchangeRate($cart['currency']);
        // }

        $language = 'NL';
        $timestamp = gmdate('Y-m-d\TH:i:sP');
        $shopName  = $this->getShopName();

        // Start building XML
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;

        $cxml = $dom->createElement('cXML');
        // $cxml->setAttribute('payloadID', uniqid());
        $cxml->setAttribute('payloadID', $cart->cartId);
        $cxml->setAttribute('xml:lang', $language);
        $cxml->setAttribute('timestamp', $timestamp);
        $dom->appendChild($cxml);

        // Header
        $header = $dom->createElement('Header');
        $cxml->appendChild($header);

        $header->appendChild($this->createCredentialNode($dom, 'From', SessionController::get('cxml_to')));
        $header->appendChild($this->createCredentialNode($dom, 'To', SessionController::get('cxml_from')));

        $sender = $dom->createElement('Sender');
        $sender->appendChild($this->createCredentialNode($dom, null, $shopName));
        $userAgent = $dom->createElement('UserAgent', 'Propeller');
        $sender->appendChild($userAgent);
        $header->appendChild($sender);

        // Message
        $message = $dom->createElement('Message');
        $message->setAttribute('deploymentMode', $deploymentMode);
        $cxml->appendChild($message);

        $punchOut = $dom->createElement('PunchOutOrderMessage');
        $message->appendChild($punchOut);

        $punchOut->appendChild($dom->createElement('BuyerCookie', htmlspecialchars($buyerCookie)));

        // Header info
        $headerNode = $dom->createElement('PunchOutOrderMessageHeader');
        $headerNode->setAttribute('operationAllowed', 'create');
        $headerNode->setAttribute('quoteStatus', 'final');

        // Totals
        $total = $dom->createElement('Total');
        $money = $dom->createElement('Money', $this->round2($cart->total->totalGross * $exchangeRate));
        $money->setAttribute('currency', PropellerHelper::currency_abbr());
        $total->appendChild($money);
        $headerNode->appendChild($total);

        // Shipping
        $shipping = $dom->createElement('Shipping');
        $shipMoney = $dom->createElement('Money', $this->round2($cart->postageData->price * $exchangeRate));
        $shipMoney->setAttribute('currency', PropellerHelper::currency_abbr());
        $shipping->appendChild($shipMoney);
        $desc = $dom->createElement('Description', $cart->postageData->method ?? '');
        $desc->setAttribute('xml:lang', $language);
        $shipping->appendChild($desc);
        $headerNode->appendChild($shipping);

        // Tax
        $totalNet = $cart->total->totalNet;
        $totalGross = $cart->total->totalGross;
        $totalBTW = $totalNet - $totalGross;
        
        $tax = $dom->createElement('Tax');
        $taxMoney = $dom->createElement('Money', $this->round2($totalBTW * $exchangeRate));
        $taxMoney->setAttribute('currency', PropellerHelper::currency_abbr());
        $tax->appendChild($taxMoney);
        $taxDesc = $dom->createElement('Description', $cart->taxLevels[0]->taxPercentage ?? '');
        $taxDesc->setAttribute('xml:lang', $language);
        $tax->appendChild($taxDesc);
        $headerNode->appendChild($tax);

        $punchOut->appendChild($headerNode);

        // Products
        if (!empty($cart->items)) {
            $lineNumber = 1;

            $productController = new ProductController();

            $skus = array_map(function ($item) {
                return trim($item->product->sku ?? $item->product->supplierCode ?? '');
            }, $cart->items);

            $products_search = $productController->get_products(['skus' => $skus, 'offset' => count($skus)], true);

            foreach ($cart->items as $item) {
                // $splitLine = $item['split_orderline'] ?? false;
                $splitLine = false;
                $quantity = $item->quantity ?? 1;

                if (!is_null($item->bundle)) {
                    $bundle_skus = array_map(function ($bundleItem) {
                        return trim($bundleItem->product->sku ?? $bundleItem->product->supplierCode ?? '');
                    }, $item->bundle->items);

                    $bundle_products_search = $productController->get_products(['skus' => $bundle_skus, 'offset' => count($skus)], true);

                    foreach ($item->bundle->items as $bundleItem) {
                        $bundleItem->product = new Product($bundleItem->product);
                        $bundleQuantity = $bundleItem->quantity ?? 1;

                        $bundleItem_product = array_filter($bundle_products_search->items, function ($prod) use ($bundleItem) {
                            $sku = trim($bundleItem->product->sku ?? $bundleItem->product->supplierCode ?? '');
                            return $prod->sku === $sku || $prod->supplierCode === $sku;
                        });

                        $bundleItem->product = current($bundleItem_product);
                        $itemNode = $this->createBundleOrderLine($dom, $item->bundle, $bundleItem, $cart, $lineNumber, $exchangeRate, $bundleQuantity, $uom_attr, $classification);
                        $punchOut->appendChild($itemNode);
                        $lineNumber++;
                    }
                } else {
                    $item_product = array_filter($products_search->items, function ($prod) use ($item) {
                        $sku = trim($item->product->sku ?? $item->product->supplierCode ?? '');
                        return $prod->sku === $sku || $prod->supplierCode === $sku;
                    });

                    $item->product = current($item_product);
                    
                    $itemNode = $this->createOrderLine($dom, $item, $cart, $lineNumber, $exchangeRate, $quantity, $uom_attr, $classification);
                    $punchOut->appendChild($itemNode);
                    $lineNumber++;
                }
                

                // if ($splitLine && $quantity > 1) {
                //     for ($i = 0; $i < $quantity; $i++) {
                //         $itemNode = $this->createOrderLine($dom, $item, $cart, $lineNumber, $exchangeRate, 1);
                //         $punchOut->appendChild($itemNode);
                //         $lineNumber++;
                //     }
                // } else {
                //     $itemNode = $this->createOrderLine($dom, $item, $cart, $lineNumber, $exchangeRate, $quantity);
                //     $punchOut->appendChild($itemNode);
                //     $lineNumber++;
                // }
            }
        }

        $order_xml = $dom->saveXML();

        $this->logPunchout('OrderRequest', $order_xml);

        return $order_xml;
    }

    /**
     * Create an <ItemIn> order line.
     */
    protected function createOrderLine(DOMDocument $dom, object $item, object $cart, int $lineNumber, float $exchangeRate, int $quantity, string $uom_attr, array $classification)
    {
        $language = 'NL';
        $item->product = new Product($item->product);

        $itemIn = $dom->createElement('ItemIn');
        $itemIn->setAttribute('quantity', $quantity);
        $itemIn->setAttribute('lineNumber', $lineNumber);

        $itemID = $dom->createElement('ItemID');
        $supplierPartID = trim($item->product->sku ?? $item->product->supplierCode ?? '');
        $itemID->appendChild($dom->createElement('SupplierPartID', $supplierPartID));
        $itemIn->appendChild($itemID);

        $itemDetail = $dom->createElement('ItemDetail');

        $unitPrice = $dom->createElement('UnitPrice');
        $money = $dom->createElement('Money', $this->round2($item->price * $exchangeRate));
        $money->setAttribute('currency', PropellerHelper::currency_abbr());
        $unitPrice->appendChild($money);
        $itemDetail->appendChild($unitPrice);

        $desc = $dom->createElement('Description');
        $desc->setAttribute('xml:lang', $language);
        $desc->appendChild($dom->createCDATASection($item->product->get_name() ?? ''));
        $itemDetail->appendChild($desc);

        $itemDetail->appendChild($dom->createElement('UnitOfMeasure', $item->product->get_track_attr_value($uom_attr) ?? 'EA'));

        $classificationDom = $dom->createElement('Classification', substr($item->product->get_track_attr_value($classification['code']), 0, 8) ?? '');
        $classificationDom->setAttribute('domain', isset($classification['domain']) && !empty($classification['domain']) ? $classification['domain'] : 'UNSPSC');
        
        // if (isset($classification['code']) && !empty($classification['code']))
        //     $classificationDom->appendChild();
        // if (isset($classification['value']) && !empty($classification['value']))
        //     $classificationDom->setAttribute('value', $item->product->get_track_attr_value($classification['value']) ?? '');

        $itemDetail->appendChild($classificationDom);

        $itemDetail->appendChild($dom->createElement('ManufacturerName', $item->product->manufacturer));

        $itemIn->appendChild($itemDetail);

        return $itemIn;
    }

    /**
     * Create an <ItemIn> order line.
     */
    protected function createBundleOrderLine(DOMDocument $dom, object $bundle, object $item, object $cart, int $lineNumber, float $exchangeRate, int $quantity, string $uom_attr)
    {
        $language = 'NL';
        $item->product = new Product($item->product);

        $itemIn = $dom->createElement('ItemIn');
        $itemIn->setAttribute('quantity', $quantity);
        $itemIn->setAttribute('lineNumber', $lineNumber);

        $itemID = $dom->createElement('ItemID');
        $supplierPartID = trim($item->product->sku ?? $item->product->supplierCode ?? '');
        $itemID->appendChild($dom->createElement('SupplierPartID', $supplierPartID));
        $itemIn->appendChild($itemID);

        $itemDetail = $dom->createElement('ItemDetail');

        $unitPrice = $dom->createElement('UnitPrice');
        $money = $dom->createElement('Money', $this->round2($item->isLeader == 'Y' ? $bundle->price->gross : 0.00 * $exchangeRate));
        $money->setAttribute('currency', PropellerHelper::currency_abbr());
        $unitPrice->appendChild($money);
        $itemDetail->appendChild($unitPrice);

        $desc = $dom->createElement('Description');
        $desc->setAttribute('xml:lang', $language);
        $desc->appendChild($dom->createCDATASection($item->product->get_name() ?? ''));
        $itemDetail->appendChild($desc);

        $itemDetail->appendChild($dom->createElement('UnitOfMeasure', $item->product->get_track_attr_value($uom_attr) ?? 'EA'));

        $classification = $dom->createElement('Classification', $item->product->turnoverGroup ?? '');
        $classification->setAttribute('domain', isset($classification['domain']) && !empty($classification['domain']) ? $classification['domain'] : 'UNSPSC');

        if (isset($classification['code']) && !empty($classification['code']))
            $classification->nodeValue = $classification['code'];
        if (isset($classification['value']) && !empty($classification['value']))
            $classification->setAttribute('value', $classification['value']);
        
        $itemDetail->appendChild($classification);

        $itemDetail->appendChild($dom->createElement('ManufacturerName', $item->product->manufacturer));

        $itemIn->appendChild($itemDetail);

        return $itemIn;
    }

    /**
     * Create a <Credential> node.
     */
    protected function createCredentialNode(DOMDocument $dom, ?string $wrapperTag, string $identity): DOMElement
    {
        $credential = $dom->createElement('Credential');
        $credential->setAttribute('domain', 'NetworkID');
        $credential->appendChild($dom->createElement('Identity', $identity));

        if ($wrapperTag) {
            $wrapper = $dom->createElement($wrapperTag);
            $wrapper->appendChild($credential);
            return $wrapper;
        }

        return $credential;
    }

    public function convertCartToCXML(object $cart, string $hook_url, string $button_label, string $uom_attr = '', array $classification = [], string $deploymentMode = 'test') {
        $cxml = $this->buildOrderCxml($cart, SessionController::get('buyer_cookie'), $deploymentMode, $uom_attr, $classification); 

        $html = "<form action=\"{$hook_url}\" method=\"POST\" name=\"cXmlForm\" id=\"cXmlForm\" class=\"propeller-oci-form-btn\" data-cart=\"" . $cart->cartId . "\">";
        $html .= "<textarea name=\"cxml-urlencoded\" style=\"display: none; visibility: hidden\">{$cxml}</textarea>";
        $html .= "<button type=\"submit\" class=\"btn btn-checkout\" id=\"btnCxmlSubmit\">{$button_label}</button>";
        $html .= "</form>";

        return $html;
    }

    protected function validatePunchoutCredentials(string $identity, string $sharedSecret, User $user)
    {
        $userSharedSecret = $user->get_track_attr_value($user::CXML_SHARED_SECRED_ATTR);
        
        return $sharedSecret === $userSharedSecret;
    }

    protected function getPunchoutStartUrl(string $sessionId, string $buyerCookie, string $returnUrl, User $user, $from, $to)
    {
        $userController = new UserController();
        $magic_token = $userController->create_magic_token($user, true);

        return home_url('/magic_token_login?mtoken=' . $magic_token->id . '&sid=' . $sessionId . '&buyer_cookie=' . urlencode($buyerCookie) . "&HOOK_URL=" . urlencode($returnUrl) . '&cxml_from=' . urlencode($from) . '&cxml_to=' . urlencode($to) . '&is_cxml=1');
    }

    protected function parseCxml(string $xmlString)
    {
        libxml_use_internal_errors(true);

        // Trim whitespace
        $xmlString = trim($xmlString);

        // Check if XML is double-encoded (contains &amp;amp; or &amp;lt; etc)
        // Only decode if we detect double encoding
        if (preg_match('/&amp;amp;|&amp;lt;|&amp;gt;|&amp;quot;|&amp;apos;/', $xmlString)) {
            $xmlString = html_entity_decode($xmlString, ENT_QUOTES | ENT_XML1, 'UTF-8');
        }

        // Strip any MIME headers or boundaries if XML declaration is missing
        if (strpos($xmlString, '<?xml') === false) {
            $parts = preg_split('/(<\?xml)/', $xmlString, 2, PREG_SPLIT_DELIM_CAPTURE);
            if (count($parts) >= 2) {
                $xmlString = $parts[1] . ($parts[2] ?? '');
            }
        }

        // Remove the DOCTYPE declaration to prevent DTD lookup
        $xmlString = preg_replace('/<!DOCTYPE[^>]+>/', '', $xmlString);

        // Try to parse the XML
        $xml = simplexml_load_string($xmlString, 'SimpleXMLElement', LIBXML_NOCDATA | LIBXML_NONET);
        if ($xml === false) {
            foreach (libxml_get_errors() as $error) {
                error_log("cXML parse error: " . trim($error->message));
            }
            libxml_clear_errors();
            return null;
        }

        return $xml;
    }

    protected function logPunchout(string $type, string $content): void
    {
        try {
            $wp_filesystem = PropellerHelper::wp_filesys();

            // Normalize plugin dir and use forward slashes
            $dir = trailingslashit(PROPELLER_PLUGIN_DIR) . 'logs';
            $dir = wp_normalize_path($dir);
            
            $filename = $type . '_' . gmdate('Ymd_His') . '.xml';
            $file = $dir . '/' . $filename;

            // Create directory if missing
            if (!$wp_filesystem->is_dir($dir)) {
                $created = $wp_filesystem->mkdir($dir, FS_CHMOD_DIR);
                if (!$created) {
                    propel_log("logPunchout: Failed to create directory {$dir}");
                    if (!empty($wp_filesystem->errors)) {
                        propel_log('logPunchout: WP_Filesystem errors: ' . print_r($wp_filesystem->errors, true));
                    }
                    return;
                }
            }

            // Write file
            $written = $wp_filesystem->put_contents($file, $content, FS_CHMOD_FILE);

            if ($written === false) {
                propel_log("logPunchout: Failed to write file {$file}");
                if (!empty($wp_filesystem->errors)) {
                    propel_log('logPunchout: WP_Filesystem errors: ' . print_r($wp_filesystem->errors, true));
                }
                return;
            }

            propel_log("logPunchout: Wrote file {$file}");
        } 
        catch (Exception $e) {
            propel_log("logPunchout exception: {$e->getMessage()}");
        }
    }

    protected function buildErrorResponse(string $code, string $message): string
    {
        $timestamp = gmdate('Y-m-d\TH:i:sP');

        return sprintf(
            '<?xml version="1.0"?>
            <cXML timestamp="%1$s">
            <Response>
                <Status code="%2$s" text="%3$s"/>
            </Response>
            </cXML>',
            esc_attr($timestamp),
            esc_attr($code),
            esc_attr($message)
        );
    }

    /**
     * Stub: Get exchange rate for currency.
     * Replace this with actual conversion logic.
     */
    protected function getExchangeRate(string $currency): float
    {
        // Example: in production, fetch from an API or DB
        $rates = [
            'USD' => 1.05,
            'GBP' => 0.85,
            'CHF' => 0.95,
        ];
        return $rates[strtoupper($currency)] ?? 1.0;
    }

    /**
     * Stub: Get shop name.
     * Replace with logic from WordPress options or plugin settings.
     */
    protected function getShopName(): string
    {
        return get_bloginfo('name');
    }

    protected function round2(float $value, int $precision = 2): float
    {
        return round($value, $precision);
    }
}