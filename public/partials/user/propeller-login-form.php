<?php
if ( ! defined( 'ABSPATH' ) ) exit;
use Propeller\Includes\Controller\PageController;
use Propeller\Includes\Controller\SessionController;
use Propeller\Includes\Enum\PageType;
use Propeller\Includes\Controller\UserController;
?>
<div class="container-fluid px-0 propeller-login-wrapper">
    <div class="row">
        <div class="col-12">
            <?php if (UserController::is_propeller_logged_in()) { ?>
                <div><?php echo esc_html(__("You are already logged in", 'propeller-ecommerce-v2')); ?></div>
            <?php  } else { ?>
                <div class="text-end">
                    <a href="<?php echo esc_url($this->buildUrl('', PageController::get_slug(PageType::FORGOT_PASSWORD_PAGE))); ?>" class="btn-forgot-password"><?php echo esc_html(__('Forgot password?', 'propeller-ecommerce-v2')); ?></a>
                </div>
                <form name="login" class="form-handler login-form page-login-form" method="post">
                    <input type="hidden" name="action" value="do_login">

                    <?php if (SessionController::has('login_referrer')) { ?>
                        <input type="hidden" name="referrer" value="<?php echo esc_url(SessionController::get('login_referrer')); ?>">
                    <?php } ?>

                    <fieldset class="personal px-0">
                        <div class="row form-group">
                            <div class="col-form-fields col-12">
                                <div class="row g-3">
                                    <div class="col-12 form-group col-user-mail form-group-input">
                                        <label class="form-label" for="field_username"><?php echo esc_html(__('E-mail address', 'propeller-ecommerce-v2')); ?>*</label>
                                        <input type="email" name="user_mail" value="" pattern="[a-z0-9._%+\-]+@[a-z0-9.\-]+\.[a-z]{2,}$" placeholder="<?php echo esc_html(__('E-mail address', 'propeller-ecommerce-v2')); ?>*" class="form-control required email" id="field_username" autocomplete="off" autocorrect="off" spellcheck="false">
                                        <span class="input-user-message"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-form-fields col-12">
                                <div class="row g-3">
                                    <div class="col-12 form-group col-user-password form-group-input">
                                        <label class="form-label" for="field_password"><?php echo esc_html(__('Password', 'propeller-ecommerce-v2')); ?>*</label>
                                        <input type="password" name="user_password" value="" placeholder="<?php echo esc_html(__('Password', 'propeller-ecommerce-v2')); ?>*" class="form-control required" id="field_password" minlength="6" autocomplete="off" autocorrect="off" spellcheck="false">
                                        <span class="input-pass-message"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </fieldset>
                    <div class="row form-group form-group-submit">
                        <div class="col-form-fields col-12">
                            <div class="row g-3 align-items-center">
                                <div class="col-12">
                                    <input type="submit" class="btn-blue btn-proceed" value="<?php echo esc_attr(__('Log in', 'propeller-ecommerce-v2')); ?>">
                                </div>

                            </div>
                        </div>
                    </div>
                </form>
            <?php } ?>
        </div>
    </div>
</div>
<?php require $this->load_template('partials', '/other/propeller-toast.php'); ?>
