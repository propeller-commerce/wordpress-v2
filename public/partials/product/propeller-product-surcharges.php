<?php
if ( ! defined( 'ABSPATH' ) ) exit;
use Propeller\Includes\Enum\SurchargeType;
use Propeller\PropellerHelper;

if (isset($product->surcharges) && is_array($product->surcharges) && sizeof($product->surcharges)) { ?>
    <ul class="product-surcharges">
        <?php
        echo esc_html(__('Additional surcharges:', 'propeller-ecommerce-v2'));
        foreach ($product->surcharges as $surcharge) {
            $name = isset($surcharge->names) ? $surcharge->names[0]->value : $surcharge->name[0]->value;
            $quantity = 1;
            if (isset($product->quantity) && $product->quantity > 0)
                $quantity = $product->quantity;

            echo wp_kses_post('<li>');
            if ($surcharge->type == SurchargeType::FLATFEE)
                echo esc_html($quantity . ' x ' . PropellerHelper::currency() . ' ' . PropellerHelper::formatPrice($surcharge->value) . ' (' . $name . ') ');
            else if ($surcharge->type == SurchargeType::PERCENTAGE)
                echo esc_html($quantity . ' x ' . $surcharge->value . '%' . ' (' . $name . ') ');

            echo wp_kses_post('</li>');
        } ?>
    </ul>
<?php } ?>
