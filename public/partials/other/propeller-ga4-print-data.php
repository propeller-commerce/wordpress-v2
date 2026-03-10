<?php
if ( ! defined( 'ABSPATH' ) ) exit; ?>
<script>
    window.ga4data = <?php echo wp_json_encode($this->analytics->toJson($data_type)); ?>;
</script>