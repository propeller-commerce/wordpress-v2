<?php
if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="row propeller-recently-viewed-slider" id="recentlyViewedProducts">
    <div class="col-12">				 
        <h2 class="product-info-title"><?php echo esc_html( __('Recenlty viewed', 'propeller-ecommerce-v2') ); ?></h2>	
            
        <!-- <button class="btn-clear" id="oClearLastViewed">
            <span aria-hidden="true">&times;</span> <?php echo esc_html( __('Clear', 'propeller-ecommerce-v2') ); ?>
        </button> -->     
    </div>	
    <div class="col-12 slick-recently-viewed" id="product-recently-viewed-slider">
        <?php include $this->partials_dir . '/other/propeller-recent-viewed-products.php'; ?>
    </div>
</div>
<?php 
    $ids = [];
    $recently_viewed_ids = $this->get_cookie(PROPELLER_RECENT_PRODS_COOKIE);
    if ($recently_viewed_ids && !empty($recently_viewed_ids)) {
        $ids = explode(',', $recently_viewed_ids);

        for ($i = 0; $i < count($ids); $i++) {
            if (!empty($ids[$i]) && is_numeric($ids[$i]))
                $ids[$i] = (int) $ids[$i];
        }
            
    }
?>
<script type="text/javascript">
    if (typeof window.slider_recent_products == 'undefined')
        window.slider_recent_products = [];

    <?php foreach ($ids as $id) { ?>
        window.slider_recent_products.push('<?php echo esc_attr($id); ?>');
    <?php } ?>
</script>