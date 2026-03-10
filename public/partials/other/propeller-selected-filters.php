<?php
if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="row g-0">
    <div class="col-12 mb-4 mt-4">
        <?php

use Propeller\PropellerHelper;

            if(sizeof($selected_filters)) { ?>
                <span class="label"> <?php echo esc_html( __('Selected filters', 'propeller-ecommerce-v2') ); ?> </span> 
            <?php } 
            
            foreach ($selected_filters as $selected_filter) { 

                if ($selected_filter->filter->type == 'price') {
            ?>
                <a class="btn-active-filter"
                    data-filter="<?php echo esc_html( $selected_filter->filter->type ); ?>" 
                    data-value="<?php echo esc_attr( $selected_filter->value ); ?>" 
                    data-type="<?php echo esc_html( $selected_filter->filter->type ); ?>">
                        <span class="active-filter-name"><?php echo esc_html( __('Price', 'propeller-ecommerce-v2') ); ?>:
                            <?php echo esc_html( PropellerHelper::currency() . $selected_filter->values->from ); ?> - 
                            <?php echo esc_html( PropellerHelper::currency() . $selected_filter->values->to ); ?></span>
                        <svg class="icon icon-svg" aria-hidden="true">
                            <use xlink:href="#shape-close"></use>
                        </svg>
                </a>
            <?php
                } else {
        ?>
            <a class="btn-active-filter"
                data-filter="<?php echo esc_html( $selected_filter->filter->attributeDescription->name ); ?>" 
                data-value="<?php echo esc_attr( $selected_filter->value ); ?>" 
                data-type="<?php echo esc_html( $selected_filter->filter->type ); ?>">
                    <span class="active-filter-name"><?php echo esc_html( $selected_filter->value ); ?></span>
                    <svg class="icon icon-svg" aria-hidden="true">
                        <use xlink:href="#shape-close"></use>
                    </svg>
            </a>
        <?php } } ?>
        
        <?php if(sizeof($selected_filters)) { ?>
            <a class="btn-remove-active-filters"><?php echo esc_html( __('Clear all filters', 'propeller-ecommerce-v2') ); ?></a>
        <?php } ?>
    </div>
</div>