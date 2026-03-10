<?php
if ( ! defined( 'ABSPATH' ) ) exit; ?>
<!-- Global site tag (gtag.js) - Google Analytics -->
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', '<?php echo esc_js( $ga4_key ); ?>');
</script>