<?php
if ( ! defined( 'ABSPATH' ) ) exit;
if (!is_null($cart->bonusItems) && sizeof($cart->bonusItems)) { ?>
    <div class="container-fluid px-0 checkout-bonus-wrapper">
        <?php 
            apply_filters('propel_shopping_cart_bonus_items_title', $cart, $obj);

            foreach ($cart->bonusItems as $bonusItem) { 
                apply_filters('propel_shopping_cart_bonus_item', $bonusItem, $cart, $obj);
            } 
        ?>
    </div>
<?php } ?>
