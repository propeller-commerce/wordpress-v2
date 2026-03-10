<?php
if ( ! defined( 'ABSPATH' ) ) exit; ?>
<script>
    // Pass Firebase config to JavaScript
    window.propellerFirebaseConfig = <?php echo wp_json_encode($config); ?>;
</script>