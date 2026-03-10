<?php 
    if ( ! defined( 'ABSPATH' ) ) exit;
    
    function get_custom_logo_url()
    {
        $custom_logo_id = get_theme_mod( 'custom_logo' );
        $image = wp_get_attachment_image_src( $custom_logo_id , 'full' );
        return $image[0];
    }
?>
<tr>
    <td style="text-align:left;font-size:0;direction:ltr;padding:0;">
        <table role="presentation" class="contents" style="border-spacing:0;width:100%;background:#ffffff;">
            <tr>
                <td valign="middle" align="left" style="padding-bottom:20px;padding-right:20px;padding-left:20px;padding-top:20px;direction:ltr;text-align:left;">
                    <a href="<?php echo esc_url(get_site_url()); ?>">
                        <img src="<?php echo esc_url(get_custom_logo_url()); ?>" style="width:auto" alt="<?php echo esc_attr(get_bloginfo('name')); ?>"/>
                    </a>
                </td>
            </tr>
        </table>
    </td>
</tr>