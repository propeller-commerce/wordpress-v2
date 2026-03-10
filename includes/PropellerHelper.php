<?php

namespace Propeller;

if ( ! defined( 'ABSPATH' ) ) exit;

class PropellerHelper
{
    public static function currency()
    {
        do_action('propel_currency');

        if (defined('PROPELLER_CURRENCY'))
            return PROPELLER_CURRENCY;

        return '&euro;';
    }

    public static function currency_abbr()
    {
        $currency = self::currency();

        foreach (\Propeller\Includes\Enum\Currency::$currencies as $abbr => $symbol) {
            if ($symbol === $currency)
                return $abbr;
        }

        return '&euro;';
    }

    public static function formatPrice($price)
    {
        return number_format($price, 2, ',', '.');
    }

    public static function formatPriceGTM($price)
    {
        return number_format($price, 2, '.', '.');
    }

    public static function percentage($percent, $total)
    {
        return ($percent / 100) * $total;
    }

    public static function percentage_from_values($num_amount, $num_total)
    {
        $taxAmount = $num_total - $num_amount;
        $taxPercentage = ($taxAmount / $num_amount) * 100;

        $count = number_format($taxPercentage, 0);
        return $count;
    }

    public static function percentage_from_price($new_price, $original_price)
    {
        $val = ($new_price - $original_price) / $original_price * 100;

        if ($val < 0) $val *= -1;

        return self::formatFloat($val);
    }

    public static function formatFloat($number)
    {
        return number_format($number, 2, ',', '.');
    }

    public static function spareparts_active()
    {
        include_once(ABSPATH . 'wp-admin/includes/plugin.php');

        return is_plugin_active('propeller-sparepartslive/propeller-sparepartslive.php');
    }

    public static function get_pdf_url($filename, $filepath, $filesurl, $file_data)
    {
        if (!self::wp_filesys()->exists($filepath))
            self::wp_filesys()->put_contents($filepath, $file_data);

        return $filesurl . $filename;
    }

    public static function get_uploads_dir()
    {
        $upload_dir = wp_upload_dir();

        return $upload_dir['basedir'];
    }

    public static function get_uploads_url()
    {
        $upload_dir = wp_upload_dir();

        return $upload_dir['baseurl'];
    }

    public static function wp_filesys()
    {
        // Ensure WP_Filesystem is available
        if (!function_exists('WP_Filesystem')) {
            require_once ABSPATH . 'wp-admin/includes/file.php';
        }

        global $wp_filesystem;

        // Initialize if needed
        if (empty($wp_filesystem)) {
            WP_Filesystem();
        }

        if (empty($wp_filesystem)) {
            error_log("logPunchout: WP_Filesystem not initialized");
            return;
        }

        return $wp_filesystem;
    }

    public static function random_string($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $characters_length = strlen($characters);
        $random_string = '';

        for ($i = 0; $i < $length; $i++) {
            $random_string .= $characters[random_int(0, $characters_length - 1)];
        }

        return $random_string;
    }

    public static function days()
    {
        return [
            0 => __('Sunday', 'propeller-ecommerce-v2'),
            1 => __('Monday', 'propeller-ecommerce-v2'),
            2 => __('Tuesday', 'propeller-ecommerce-v2'),
            3 => __('Wednesday', 'propeller-ecommerce-v2'),
            4 => __('Thursday', 'propeller-ecommerce-v2'),
            5 => __('Friday', 'propeller-ecommerce-v2'),
            6 => __('Saturday', 'propeller-ecommerce-v2')
        ];
    }

    public static function months()
    {
        return [
            1 => __("January", 'propeller-ecommerce-v2'),
            2 => __("February", 'propeller-ecommerce-v2'),
            3 => __("March", 'propeller-ecommerce-v2'),
            4 => __("April", 'propeller-ecommerce-v2'),
            5 => __("May", 'propeller-ecommerce-v2'),
            6 => __("June", 'propeller-ecommerce-v2'),
            7 => __("July", 'propeller-ecommerce-v2'),
            8 => __("August", 'propeller-ecommerce-v2'),
            9 => __("September", 'propeller-ecommerce-v2'),
            10 => __("October", 'propeller-ecommerce-v2'),
            11 => __("November", 'propeller-ecommerce-v2'),
            12 => __("December", 'propeller-ecommerce-v2')
        ];
    }

    public static function get_sort_url($field, $current_field, $current_order)
    {
        $new_order = ($field === $current_field && $current_order === 'DESC') ? 'ASC' : 'DESC';
        $params = array_merge($_GET, ['sort_field' => $field, 'sort_order' => $new_order]);
        unset($params['ppage']);
        return '?' . http_build_query($params);
    }

    public static function parse_localized_date($date_string)
    {
        $localized_months = [
            'januari' => 'January', 'februari' => 'February', 'maart' => 'March',
            'april' => 'April', 'mei' => 'May', 'juni' => 'June',
            'juli' => 'July', 'augustus' => 'August', 'september' => 'September',
            'oktober' => 'October', 'november' => 'November', 'december' => 'December',
        ];

        $normalized = str_ireplace(array_keys($localized_months), array_values($localized_months), $date_string);

        // Fix missing space between year and time (e.g. "20241:00" → "2024 1:00")
        $normalized = preg_replace('/(\d{4})(\d{1,2}:\d{2})/', '$1 $2', $normalized);

        return new \DateTime($normalized);
    }

    public static function get_sort_icon($field, $current_field, $current_order)
    {
        if ($field !== $current_field) {
            return '<span class="sort-icon sort-none">↕</span>';
        }
        return $current_order === 'ASC'
            ? '<span class="sort-icon sort-asc">↑</span>'
            : '<span class="sort-icon sort-desc">↓</span>';
    }
}
