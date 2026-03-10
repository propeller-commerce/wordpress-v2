<?php
if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="row">
    <div class="col-12">
        <h3><?php echo esc_html( __('Downloads', 'propeller-ecommerce-v2') ); ?></h3>
    </div>
    <div class="col-12">
        <?php 
            if ($product->has_documents()) { 
                foreach ($product->documents as $doc) {
        ?>
            <div class="row g-0 product-specs">
                <div class="col-sm-6">
                    <?php 
                        $found = array_filter($doc->description, function($obj) { return strtolower($obj->language) == strtolower(PROPELLER_LANG); });

                        if (!count($found)) 
                            $found = array_filter($doc->description, function($obj) { return !empty($obj->value); });
                        
                        if (count($found)) {
                            echo esc_html(current($found)->value);
                        } else {
                            echo esc_html(basename($doc->documents[0]->originalUrl));
                        }
                    ?>
                </div>
                <div class="col-6">
                    <a href="<?php echo esc_url($doc->documents[0]->originalUrl); ?>" target="_blank">
                        <?php echo esc_html( __('Download PDF', 'propeller-ecommerce-v2') ); ?>
                    </a>
                </div>
            </div>
            
        <?php
                }
        } else { ?>
            <p><?php echo esc_html( __('No downloads', 'propeller-ecommerce-v2') ); ?></p>
        <?php } ?>
    </div>
</div>