<?php

if ( ! defined( 'ABSPATH' ) ) exit;

function propel_sitemap_cron_schedules( $schedules ) {
    $schedules['dailt'] = array(
        'interval' => 86400,
        'display'  => 'Once a day',
    );

    return $schedules;
}

add_filter( 'cron_schedules', 'propel_sitemap_cron_schedules', 10, 1 );


function propel_sitemap_cron() {
    require_once(plugin_dir_path(__FILE__) . '/sitemap-cron.php');
}

add_action( 'propel_sitemap_cron', 'propel_sitemap_cron' );

if (!wp_next_scheduled('propel_sitemap_cron'))
    wp_schedule_event(strtotime('00:00:00'), 'daily', 'propel_sitemap_cron');