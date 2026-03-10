<?php
if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div id="pane-spec" class="product-pane">
    <div class="row">
        <div class="col-12">
            <h3><?php echo esc_html( __('Specifications', 'propeller-ecommerce-v2') ); ?></h3>
        </div>
        <div class="col-12">
            <?php if(!empty($cluster_product->eanCode)) { ?> 
                <div class="row g-0 product-specs">
                    <div class="col-sm-6">
                    <?php echo esc_html( __('EAN code', 'propeller-ecommerce-v2') ); ?>
                    </div>
                    <div class="col-auto">
                        <?php echo esc_html($cluster_product->eanCode); ?>
                    </div>
                </div>
            <?php } ?>
            <?php if(!empty($cluster_product->manufacturer)) { ?> 
                <div class="row g-0 product-specs">
                    <div class="col-sm-6">
                    <?php echo esc_html( __('Brand', 'propeller-ecommerce-v2') ); ?>
                    </div>
                    <div class="col-auto">
                        <?php echo esc_html($cluster_product->manufacturer); ?>
                    </div>
                </div>
            <?php } ?>
            <?php 

            if($cluster_product->has_attributes()){
                foreach ($cluster_product->get_attributes() as $attribute) {
                    if (($attribute->get_type() == 'text' || 
                         $attribute->get_type() == 'list' || 
                         $attribute->get_type() == 'enumlist' ||
                         $attribute->get_type() == 'integer' ||
                         $attribute->get_type() == 'decimal') && $attribute->has_value()){ ?>
                            <div class="row g-0 product-specs">
                                <div class="col-sm-6">
                                    <?php echo esc_html($attribute->get_description()); ?>
                                </div>
                                <div class="col-6">
                                    <?php echo esc_html($attribute->get_value()); ?>
                                </div>
                            </div>
                        <?php 
                    }
                }
            }
            ?>
        </div>
    </div>
</div>