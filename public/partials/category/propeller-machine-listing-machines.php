<?php
if ( ! defined( 'ABSPATH' ) ) exit;
if (!count($machines) && $parts->itemsFound == 0) { ?>
    <h1 class="title col-12"><?php echo esc_html(__("No installations found", "propeller-ecommerce-v2")); ?></h1>
    <div class="col-12">
        <a href="#" class="machines-back">
            &laquo; <?php echo esc_html(__("Go back", "propeller-ecommerce-v2")); ?>
        </a>
    </div>
<?php } else { ?>

<?php foreach ($machines as $machine) { 
    if (!count($machine->slug)) // skip products without slug, probably not translated
        continue;
?>
    <div class="propeller-list-item col-12 col-sm-6 col-xl-4">
        <?php echo esc_html( wp_kses_post(apply_filters('propel_machine_card', $machine, $obj)) ); ?>
    </div>
<?php } ?>

<?php if ($parts->itemsFound > 0) {
    foreach ($parts->items as $part) { 
?>
    <div class="propeller-list-item col-12 col-sm-6 col-xl-4">
        <?php echo esc_html( wp_kses_post(apply_filters('propel_product_card', $part->product, $obj)) ); ?>
    </div>
<?php } } }?>
