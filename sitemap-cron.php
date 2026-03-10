<?php

if ( ! defined( 'ABSPATH' ) ) exit;

include_once 'wp-load.php';

require 'wp-content/plugins/propeller-ecommerce-v2/vendor/autoload.php';

set_time_limit(0);

propel_log('Starting sitemap cron...');

$sitemap = new \Propeller\PropellerSitemap();

$sitemap->clear_sitemap();

$sitemap->build_sitemap();

propel_log('Sitemap cron done...');