<?php
if ( ! defined( 'ABSPATH' ) ) exit;
if (!empty($data->description[0]->value)) { ?>    
    <div class="row mt-4">
        <div class="col-12">
            <div class="category-description">
                <?php
                echo wp_kses($data->description[0]->value, wp_kses_allowed_html('post'))
                ?>
            </div>
        </div>
    </div>
<?php } ?>
