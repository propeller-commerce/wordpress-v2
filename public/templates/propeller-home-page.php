<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="row propeller-homepage-slider">
    <div class="col-12 slick-homepage">
        <?php foreach ($this->products as $product) { 
            if(sizeof($product->attributes)){
                foreach ($product->attributes as $attribute) {
                    if($attribute->searchId == $atts['product_attr'] && !empty($attribute->textValue[0]->values[0])) { ?>    
                        <div>
                            <?php require $this->load_template('partials', '/product/propeller-product-card.php'); ?>
                        </div>
            <?php } 
                } 
            } 
        } ?>
    </div>
</div>