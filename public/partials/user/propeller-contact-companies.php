<?php
if ( ! defined( 'ABSPATH' ) ) exit;
use Propeller\Includes\Enum\UserTypes;

if ($user_type == UserTypes::CONTACT && sizeof($companies) > 1) {
    $company_name = '';
?>
    <svg style="display:none">
        <symbol viewBox="0 0 32 32" id="shape-header-company">
            <title><?php echo esc_html(__('Company', 'propeller-ecommerce-v2')); ?></title>
            <g fill="none" stroke-linecap="square" stroke-miterlimit="10">
                <path d="M28 29H4a3 3 0 0 1-3-3V2h11l3 5h16v19a3 3 0 0 1-3 3Z" />
                <circle data-color="color-2" cx="12" cy="14" r="3" />
                <circle data-color="color-2" cx="20.5" cy="12.5" r="2.5" />
                <path data-color="color-2" d="M12 20a6 6 0 0 0-6 6h12a6 6 0 0 0-6-6ZM21 23h5a5.5 5.5 0 0 0-8-4.387" />
            </g>
        </symbol>
    </svg>
    <div class="propeller-mini-header-buttons propeller-mini-account dropdown dropdown-menu-end contact-companies" id="propel_mini_companies">
        <a class="btn-header-account d-flex" href="#" id="header-button-companies" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <span class="account-icon">
                <svg class="icon icon-account">
                    <use class="header-shape-companies" xlink:href="#shape-header-company"></use>
                </svg>
            </span>
            <span class="account-label d-none d-md-flex">
                <span class="account-title">
                    <?php foreach ($companies as $company) { ?>
                        <?php if ($company->companyId == $default_company->companyId) { ?>
                            <?php $company_name = $company->name;
                            echo esc_html($company->name); ?>
                    <?php }
                    } ?>
                </span>
            </span>
            <span class="tooltip"><?php echo esc_html($company_name); ?></span>
        </a>

        <div class="dropdown-menu dropdown-account" aria-labelledby="header-button-companies" id="header-dropdown-companies">
            <div class="dropdown-wrapper company-menu">
                <div class="title d-flex align-items-center justify-content-between">
                    <span><?php echo esc_html(__('Switch companies', 'propeller-ecommerce-v2')); ?></span>
                    <button type="button" class="close">&times;</button>
                </div>
                <?php foreach ($companies as $company) { ?>
                    <a class="contact-company <?php echo esc_html($company->companyId == $default_company->companyId ? "active" : ""); ?>" data-id="<?php echo esc_attr($company->companyId); ?>" data-bs-toggle="modal" data-bs-target=".propel-company-switch-modal">
                        <div class="company-details">
                            <?php echo esc_html($company->name) . ' <br>&commat;' . esc_html($company->addresses[0]->city); ?>
                        </div>
                    </a>
                <?php } ?>
            </div>

        </div>
    </div>
<?php } ?>
