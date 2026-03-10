<?php
if ( ! defined( 'ABSPATH' ) ) exit;
use Propeller\Includes\Object\Cluster;

if ($cluster->cluster_type == Cluster::CLUSTER_TYPE_CONFIGURABLE) { ?>

    <?php if (!empty($cluster->defaultProduct->shortDescription[0]->value)) { ?>

        <div class="row">
            <div class="col-12">
                <div class="product-short-description">
                    <?php echo wp_kses_post($cluster->defaultProduct->shortDescription[0]->value); ?>
                </div>
            </div>
        </div>
    <?php }
} else { ?>
    <?php if (!empty($cluster->shortDescription[0]->value)) { ?>
        <div class="row">
            <div class="col-12">
                <div class="product-short-description">
                    <?php echo wp_kses_post($cluster->shortDescription[0]->value); ?>
                </div>
            </div>
        </div>
<?php }
} ?>
