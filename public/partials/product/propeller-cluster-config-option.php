<?php
if ( ! defined( 'ABSPATH' ) ) exit;
use Propeller\Includes\Controller\SessionController;
use Propeller\Includes\Enum\ProductStatus;
use Propeller\PropellerHelper;

$cluster_opts = [];
$user_prices = SessionController::get(PROPELLER_SPECIFIC_PRICES);

if (count($option->products)) {
?>
    <div class="col-12 px-0 cluster-config-details">
        <svg style="display: none;">
            <symbol viewBox="0 0 12 12" id="shape-info">
                <title><?php echo esc_html(__('Info', 'propeller-ecommerce-v2')); ?></title>
                <g data-name="Group 17765" transform="translate(-1092 -382.052)">
                    <circle data-name="Ellipse 242" cx="6" cy="6" r="6" transform="translate(1092 382.052)" fill="#000" />
                    <g data-name="Group 2926" style="isolation:isolate">
                        <path data-name="Path 2208" d="M1098.731 387.554a5.86 5.86 0 0 1-.243 1.28 6.058 6.058 0 0 0-.243 1.187q0 .279.081.279a2.133 2.133 0 0 0 .414-.168l.108-.053.153.31q-.081.071-.216.177a3.2 3.2 0 0 1-.505.288 1.571 1.571 0 0 1-.685.182.653.653 0 0 1-.5-.19.719.719 0 0 1-.18-.514 5.25 5.25 0 0 1 .226-1.152 5.488 5.488 0 0 0 .225-1.076 1.177 1.177 0 0 0-.235-.7l-.081-.106.01-.115a6.408 6.408 0 0 1 1.551-.133.791.791 0 0 1 .12.504Zm-1.028-1.475a.611.611 0 0 1-.181-.466.67.67 0 0 1 .248-.513.835.835 0 0 1 .573-.221.688.688 0 0 1 .505.177.621.621 0 0 1 .18.465.656.656 0 0 1-.257.509.857.857 0 0 1-.573.221.687.687 0 0 1-.495-.172Z" fill="#fff" />
                    </g>
                </g>
            </symbol>
        </svg>
        <h3>
            <?php
            if (isset($option->name) && is_array($option->name) && count($option->name) && !empty($option->name[0]->value)) {
                $option->required = $option->isRequired == 'Y' ? true : false;

                echo esc_html($option->name[0]->value) . ($option->required ? '*' : '');
            }
            ?>
            <?php
            if (isset($option->shortDescription) && is_array($option->shortDescription) && count($option->shortDescription) && !empty($option->shortDescription[0]->value)) {
            ?>
                <a data-bs-target="#configOptionModal_<?php echo esc_html($option->id); ?>" data-bs-toggle="modal" class="option-type-modal">
                    <svg class="d-inline-flex icon icon-info" aria-hidden="true">
                        <use xlink:href="#shape-info"></use>
                    </svg>
                </a>
            <?php }
            ?>
        </h3>

        <div class="row">
            <div class="col-12">
                <div class="cluster-config-dropdowns">
                    <div class="dropdown">

                        <select class="cluster-option-dropdown"
                            placeholder="<?php echo esc_html(__('Select an option', 'propeller-ecommerce-v2')); ?>"
                            id="option_<?php echo esc_attr($option->id); ?>"
                            name="option[<?php echo esc_attr($option->id); ?>]"
                            data-option="<?php echo esc_attr($option->id); ?>"
                            data-cluster_id="<?php echo esc_attr($cluster->clusterId); ?>"
                            data-description="<?php echo esc_html($cluster->name[0]->value); ?>"
                            data-slug="<?php echo esc_attr($cluster->get_slug()); ?>"
                            data-required="<?php echo esc_attr($option->required); ?>"
                            <?php echo esc_attr($option->required ? 'required' : ''); ?>
                            title="<?php if ($option->required) echo esc_attr(__('Please, select your option', 'propeller-ecommerce-v2')); ?>">

                            <?php
                            if ($option->required) { ?>
                                <option value=""><?php echo esc_html(__('Select', 'propeller-ecommerce-v2'));
                                                    echo esc_html(' ' . $option->name[0]->value); ?></option>
                            <?php } else if (!$option->required) { ?>
                                <option value=""><?php echo esc_html(__('No', 'propeller-ecommerce-v2'));
                                                    echo esc_html(' ' . $option->name[0]->value);
                                                    ?></option>
                            <?php } ?>
                            <?php
                            foreach ($option->products as $product) {
                                if (!isset($product->productId) || !is_numeric($product->productId))
                                    continue;
                                if ($product->status == ProductStatus::N)
                                    continue;

                                $disabled = '';

                                if ($product->orderable == 'N')
                                    $disabled = 'disabled';

                                $image = $product->has_images() ? esc_url($product->images[0]->images[0]->url) : "";

                                $selected = '';

                                // Only set selected if explicitly chosen in request data
                                // Never auto-select defaults - always force user to choose from placeholder
                                if (
                                    empty($disabled) && isset($request_data['option']) && is_array($request_data['option']) &&
                                    isset($request_data['option'][$option->id]) && (int) $request_data['option'][$option->id] == $product->productId
                                )
                                    $selected = 'selected';
                            ?>
                                <option value="<?php echo esc_attr($product->productId); ?>" <?php echo !empty($image) ? 'data-image="' . esc_url($image) . '"' : ''; ?> <?php echo esc_attr($disabled ? 'disabled' : ''); ?> <?php echo esc_attr($selected); ?>>
                                    <?php echo esc_html($product->name[0]->value); ?>
                                    (<?php echo esc_html(PropellerHelper::currency() . ' ' . PropellerHelper::formatPrice($user_prices ? $product->price->net : $product->price->gross)); ?>)
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php } ?>
