<?php
if ( ! defined( 'ABSPATH' ) ) exit; 

if ($data_type && $data_type != '') {
?>
<script>
    var dataLayer = window.dataLayer || [];
    dataLayer.push({ ecommerce: null });  // Clear the previous ecommerce object.
    dataLayer.push(<?php echo wp_json_encode($this->analytics->toJson($data_type)); ?>);
</script>
<?php } ?>