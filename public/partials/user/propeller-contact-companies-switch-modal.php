<?php
if ( ! defined( 'ABSPATH' ) ) exit;
use Propeller\Includes\Enum\UserTypes;

if ($user_type == UserTypes::CONTACT && sizeof($companies) > 1) {

?>
    <!-- Small modal -->
    <div class="modal fade bd-example-modal-sm propel-company-switch-modal" id="propel_company_switch_modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog modal-dialog-centered modal-sm rounded">
            <div class="modal-content shadow p-5">
                <div class="propel-loader">
                    <div class="d-flex justify-content-center">
                        <div class="spinner-border p-5 text-info" style="width: 3rem; height: 3rem;" role="status">
                            <span class="visually-hidden"><?php echo esc_html(__('Switch to', 'propeller-ecommerce-v2')); ?> <span class="selected-company-name"></span>, <span class="selected-company-city"></span></span>
                        </div>
                    </div>
                </div>
                <p class="h4 text-center text-secondary mt-5"><?php echo esc_html(__('Switch to', 'propeller-ecommerce-v2')); ?> <span class="selected-company-name"></span>, <span class="selected-company-city"></span></p>
            </div>
        </div>
        <textarea id="contact_companies_object" class="d-none "><?php echo esc_html(json_encode($companies)); ?></textarea>
    </div>
<?php } ?>
