<?php
if ( ! defined( 'ABSPATH' ) ) exit;
use Propeller\Includes\Controller\PageController;
use Propeller\Includes\Enum\PageType;
?>
<div class="container-fluid ps-0 propeller-login-wrapper">
    <div class="row">
        <div class="col-12 mx-auto">
            <form name="login" class="form-handler login-form page-login-form forgot-password-form" method="post">
                <input type="hidden" name="action" value="forgot_password">
                <fieldset class="personal ps-0">
                    <div class="row form-group">
                        <div class="col-form-fields col-12">
                            <div class="row g-3">
                                <div class="col-12 col-md-8 form-group col-user-mail">
                                    <label class="form-label" for="field_username"><?php echo esc_html(__('E-mail address', 'propeller-ecommerce-v2')); ?>*</label>
                                    <input type="email" name="user_mail" pattern="[a-z0-9._%+\-]+@[a-z0-9.\-]+\.[a-z]{2,}$" value="" placeholder="<?php echo esc_html(__('E-mail address', 'propeller-ecommerce-v2')); ?>*" class="form-control required" id="field_username" autocomplete="off" autocorrect="off" spellcheck="false">
                                    <span class="input-user-message"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                </fieldset>
                <div class="row form-group form-group-submit">
                    <div class="col-form-fields col-12 col-md-8">
                        <div class="row g-3 align-items-center">
                            <div class="col-auto">
                                <input type="submit" class="btn-blue btn-proceed" value="<?php echo esc_attr(__('Send', 'propeller-ecommerce-v2')); ?>">
                            </div>
                            <div class="col">
                                <a href="<?php echo esc_url($this->buildUrl('', PageController::get_slug(PageType::LOGIN_PAGE))); ?>" class="btn-forgot-password"><?php echo esc_html(__('Cancel', 'propeller-ecommerce-v2')); ?></a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<?php require $this->load_template('partials', '/other/propeller-toast.php'); ?>
