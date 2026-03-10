<?php
if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="propeller-checkout-wrapper">
    
    <?php echo esc_html( wp_kses_post(apply_filters('propel_checkout_regular_page_title', $this->cart, $this)) ); ?>

    <div class="container-fluid px-0">
        <div class="row">
            <div class="col-12 col-lg-8">
                <div class="checkout-wrapper-steps">
                    
                    <?php echo esc_html( wp_kses_post(apply_filters('propel_checkout_regular_step_1_titles', $this->cart, $this)) ); ?>

                    <div class="row">
                        <div class="col-12">
                            
                            <?php echo esc_html( wp_kses_post(apply_filters('propel_checkout_invoice_details', $this->cart, $this)) ); ?>

                            <?php echo esc_html( wp_kses_post(apply_filters('propel_checkout_regular_step_1_submit', $this->cart, $this, $slug)) ); ?>
                            
                        </div>
                    </div>
                </div>

                <?php echo esc_html( wp_kses_post(apply_filters('propel_checkout_regular_step_1_other_steps', $this->cart, $this)) ); ?>

            </div>
          
            <?php echo esc_html( wp_kses_post(apply_filters('propel_shopping_cart_totals', $this->cart, $this)) ); ?>

        </div>
    </div>
</div>