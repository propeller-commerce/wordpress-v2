<?php
if ( ! defined( 'ABSPATH' ) ) exit;
foreach ($track_and_traces as $tt) {
    $carrier_id = $tt->carrierId;
    $carrier = null;

    $carrier_found = array_filter($carriers, function ($c) use ($carrier_id) {
        return $c->id == $carrier_id;
    });

    if (count($carrier_found))
        $carrier = reset($carrier_found);

?>
    <div class="col-12 propeller-shopping-cart-wrapper">
        <a class="track-trace-link btn-continue" href="<?php echo esc_url($carrier->trackAndTraceURL . $tt->code); ?>" target="_blank">
            <?php echo esc_html(__('Track and trace', 'propeller-ecommerce-v2')); ?>
        </a>
    </div>
<?php
}