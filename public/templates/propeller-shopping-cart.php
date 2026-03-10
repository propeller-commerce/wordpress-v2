<?php

    if ( ! defined( 'ABSPATH' ) ) exit;

    use Propeller\Includes\Controller\UserController;
?>
<svg style="display:none;">
    <symbol viewBox="0 0 18 21" id="shape-delete">
        <title>Delete</title>
        <path d="M11.562 17.375h.625c.173 0 .313-.14.313-.312V6.438a.313.313 0 0 0-.313-.313h-.625a.313.313 0 0 0-.312.313v10.625c0 .172.14.312.312.312zm-6.25 0h.625c.173 0 .313-.14.313-.312V6.438a.313.313 0 0 0-.313-.313h-.625A.313.313 0 0 0 5 6.438v10.625c0 .172.14.312.312.312zM17.187 3h-4.062l-1.313-1.75a1.87 1.87 0 0 0-1.5-.75H7.187a1.87 1.87 0 0 0-1.5.75L4.375 3H.312A.313.313 0 0 0 0 3.313v.625c0 .172.14.312.312.312h.938v14.375c0 1.035.84 1.875 1.875 1.875h11.25c1.035 0 1.875-.84 1.875-1.875V4.25h.937c.173 0 .313-.14.313-.312v-.625A.313.313 0 0 0 17.187 3zm-10.5-1a.627.627 0 0 1 .5-.25h3.125c.205 0 .386.098.5.25l.75 1H5.937l.75-1zM15 18.625c0 .345-.28.625-.625.625H3.125a.625.625 0 0 1-.625-.625V4.25H15v14.375zm-6.563-1.25h.625c.173 0 .313-.14.313-.312V6.438a.313.313 0 0 0-.313-.313h-.625a.313.313 0 0 0-.312.313v10.625c0 .172.14.312.312.312z" />
    </symbol>
    <symbol viewBox="0 0 14 14" id="shape-remove">
        <title>Remove</title>
        <path d="M13.656 12.212c.41.41.41 1.072 0 1.481a1.052 1.052 0 0 1-1.485 0L7 8.5l-5.207 5.193a1.052 1.052 0 0 1-1.485 0 1.045 1.045 0 0 1 0-1.481L5.517 7.02.307 1.788a1.045 1.045 0 0 1 0-1.481 1.052 1.052 0 0 1 1.485 0L7.001 5.54 12.207.348a1.052 1.052 0 0 1 1.486 0c.41.408.41 1.072 0 1.48L8.484 7.02l5.172 5.192z" fill="#064279" />
    </symbol>
</svg>
<div class="checkout-wrapper propeller-shopping-cart-wrapper <?php echo esc_html(apply_filters('propel_shopping_cart_classes', '')); ?>" id="shoppingcart">
    <div class="container-fluid px-0 checkout-header-wrapper">

        <?php echo esc_html(apply_filters('propel_shopping_cart_title', $this->cart)); ?>

        <?php echo esc_html(apply_filters('propel_shopping_cart_info', $this->cart, $this)); ?>

    </div>

    <?php if (sizeof($this->get_items())) { ?>

        <?php echo esc_html(apply_filters('propel_shopping_cart_table_header', $this->cart, $this)); ?>

        <?php echo esc_html(apply_filters('propel_shopping_cart_table_items', $this->cart, $this)); ?>

        <?php echo esc_html(apply_filters('propel_shopping_cart_bonus_items', $this->cart, $this)); ?>

        <?php echo esc_html(apply_filters('propel_shopping_cart_action_code', $this->cart, $this)); ?>



        <div class="container-fluid px-0 shopping-cart-summary">
            <div class="row d-flex <?php if (!UserController::is_propeller_logged_in()) {
                                        echo 'flex-end-cc ';
                                    } ?>">
                <?php echo esc_html(apply_filters('propel_shopping_cart_voucher', $this->cart, $this)); ?>

                <?php echo esc_html(apply_filters('propel_shopping_cart_order_type', $this->cart, $this)); ?>

                <?php echo esc_html(apply_filters('propel_shopping_cart_totals', $this->cart, $this)); ?>

            </div>
        </div>

        <?php echo esc_html(apply_filters('propel_shopping_cart_buttons', $this->cart, $this)); ?>

    <?php } ?>
</div>