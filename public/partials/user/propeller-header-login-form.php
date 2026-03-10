<?php
if ( ! defined( 'ABSPATH' ) ) exit;
use Propeller\Includes\Controller\PageController;
use Propeller\Includes\Enum\PageType;
?>
<div class="row propeller-login-wrapper">
    <div class="col-12">
        <form name="login" class="form-handler login-form header-login-form" method="post">
            <input type="hidden" name="action" value="do_login">

            <?php
            $current_url = home_url();

            if (isset($data) && isset($data['ref']) && !empty($data['ref']))
                $current_url = $data['ref'];
            else if ($_SERVER['REQUEST_URI'] != PageController::get_slug(PageType::LOGIN_PAGE))
                $current_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            ?>

            <input type="hidden" name="referrer" value="<?php echo esc_url($current_url); ?>">

            <fieldset class="personal">
                <div class="row form-group">
                    <div class="col-form-fields col-12">
                        <div class="row g-3">
                            <div class="col-12 form-group col-user-mail">
                                <input type="email" name="user_mail" pattern="[a-z0-9._%+\-]+@[a-z0-9.\-]+\.[a-z]{2,}$" autocomplete="off" autocorrect="off" spellcheck="false" placeholder="<?php echo esc_html(__('E-mail address', 'propeller-ecommerce-v2')); ?>*" value="" class="form-control required email" id="field_username">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-form-fields col-12">
                        <div class="row g-3">
                            <div class="col-12 form-group col-user-password">
                                <input type="password" name="user_password" placeholder="<?php echo esc_html(__('Password', 'propeller-ecommerce-v2')); ?>*" value="" class="form-control required" id="field_password" minlength="6" autocomplete="off" autocorrect="off" spellcheck="false">
                            </div>
                        </div>
                    </div>
                </div>
                <!-- <div class="row form-group">
                    <div class="col-form-fields col-12">
                        <div class="row g-3">
                            <div class="col-12 form-check">
                                <label class="form-check-label">
                                    <input class="form-check-input" type="checkbox" name="save_password" value="Y" title="<?php echo esc_attr(__('Stay logged in', 'propeller-ecommerce-v2')); ?>">
                                    <span><?php echo esc_html(__('Stay logged in', 'propeller-ecommerce-v2')); ?></span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>  -->
            </fieldset>
            <div class="row form-group form-group-submit">
                <div class="col-form-fields col-12">
                    <div class="row g-3">
                        <div class="col-12">
                            <input type="submit" class="btn-green btn-proceed" value="<?php echo esc_attr(__('Log in', 'propeller-ecommerce-v2')); ?>">
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <div class="row form-group form-group-submit">
            <div class="col-form-fields col-12">
                <div class="row g-3">
                    <div class="col-12">
                        <a href="<?php echo esc_url($this->buildUrl('', PageController::get_slug(PageType::FORGOT_PASSWORD_PAGE))); ?>" class="btn-proceed btn-forgot-password"><?php echo esc_html(__('Forgot password', 'propeller-ecommerce-v2')); ?></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>