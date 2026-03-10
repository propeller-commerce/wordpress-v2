<?php
if ( ! defined( 'ABSPATH' ) ) exit;
use Propeller\Includes\Object\Cluster;

if ($cluster->cluster_type == Cluster::CLUSTER_TYPE_CONFIGURABLE) { ?>

<?php if(!empty($cluster->defaultProduct->description[0]->value)) { ?>   
    <div id="pane-desc" class="product-pane">
        <div class="row">
            <div class="col-12">
                <?php echo wp_kses_post( $cluster->defaultProduct->description[0]->value ); ?>
            </div>
        </div>
    </div>
<?php } } else { ?>
<?php if(!empty($cluster->description[0]->value)) { ?>   
    <div id="pane-desc" class="product-pane">
        <div class="row">
            <div class="col-12">
                <?php echo wp_kses_post( $cluster->description[0]->value ); ?>
            </div>
        </div>
    </div>
<?php } } ?>
