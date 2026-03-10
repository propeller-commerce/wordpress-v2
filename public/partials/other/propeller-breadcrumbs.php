<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<nav aria-label="Breadcrumb" class="page-breadcrumb <?php echo esc_html( apply_filters('propel_breadcrumb_classes', '') ); ?>">
    <ol>
        <li>
            <a href="<?php echo esc_url( get_site_url() ); ?>"><?php echo esc_html( __("Home",'propeller-ecommerce-v2') ); ?></a>
        </li>
        
        <?php if ($paths && count($paths)) {
            $index = 0;            
            foreach ($paths as $path) { 
        ?>
            <li>
                <?php if ($index == count($paths) - 1) { ?>
                    <a href="<?php echo esc_url($path[0]); ?>" aria-current="page"><?php echo esc_html($path[1]); ?></a>
                <?php } else { ?>
                    <a href="<?php echo esc_url($path[0]); ?>"><?php echo esc_html($path[1]); ?></a>
                <?php } ?>
            </li>
        <?php 
                $index++;
            } 
        }
        ?>
    </ol>
</nav>