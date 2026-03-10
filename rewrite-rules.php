<?php

if ( ! defined( 'ABSPATH' ) ) exit;

use Propeller\Includes\Controller\PageController;
use Propeller\Includes\Enum\PageType;

function propeller_rewrites_init()
{
	global $propellerSluggablePages, $propel_rw;

	$propel_rw = [];

	if (!defined('PROPELLER_ID_IN_URL'))
		\Propeller\Propeller::register_behavior();

	if ( is_array( $propellerSluggablePages ) && sizeof( $propellerSluggablePages ) ) {
		foreach ( $propellerSluggablePages as $type => $page ) {
			$qry_var = 'slug';

			switch ($type) {
				case PageType::SEARCH_PAGE:
					$qry_var = 'term';
					break;
				case PageType::BRAND_PAGE:
					$qry_var = 'manufacturer';
					break;
				default:
					$qry_var = 'slug';
					break;
			}

			if ($page == PageController::get_slug(PageType::MACHINES_PAGE)) {
				$qry_var = 'slug[]';
				$slug_pattern = '/([^/]*)';
				$slug_end_patern = '/?$';

				$rule_pattern = $page;

				$url_patern = 'index.php?pagename=' . $page;
				$url_end_pattern = '&' . $qry_var . '=$matches[{ptrn}]';

				for ($i = 0; $i < PROPELLER_MACHINES_DEPTH; $i++) {
					$rule_pattern .= $slug_pattern;
					$url_patern .= str_replace('{ptrn}', $i + 1, $url_end_pattern);

					$propel_rw[$rule_pattern . $slug_end_patern] = $url_patern;
				}
			} else {
				if ($qry_var == 'slug' && ($type == PageType::PRODUCT_PAGE || $type == PageType::CLUSTER_PAGE || $type == PageType::CATEGORY_PAGE)) {
					if (PROPELLER_ID_IN_URL)
						$propel_rw[$page . '/(\d+)/([^/]*)/?$'] = 'index.php?pagename=' . $page . '&obid=$matches[1]&' . $qry_var . '=$matches[2]';
					else
						$propel_rw[$page . '/([^/]*)/?$'] = 'index.php?pagename=' . $page . '&' . $qry_var . '=$matches[1]';
				} else
					$propel_rw[$page . '/([^/]*)/?$'] = 'index.php?pagename=' . $page . '&' . $qry_var . '=$matches[1]';
			}
		}
	}

	// Check to see if first rw propeller rule doesn't exist
	// if so, flush rw rules only once
	$rules = get_option('rewrite_rules');
	if (!isset($rules[array_key_first($propel_rw)]))
		propel_purge_caches();

	foreach ($propel_rw as $rule => $url)
		add_rewrite_rule($rule, $url, 'top');
}

add_action('init', 'propeller_rewrites_init', 1, 0);

function propel_purge_caches()
{
	flush_rewrite_rules();
}

add_action('propel_after_activation', 'propeller_rewrites_init');
add_action('propel_cache_destroyed', 'propel_purge_caches');

add_action('upgrader_process_complete', 'propel_purge_caches');
add_action('_core_updated_successfully', 'propel_purge_caches');

function propeller_query_vars($query_vars)
{
	$query_vars[] = 'obid';
	$query_vars[] = 'slug';
	$query_vars[] = 'term';
	$query_vars[] = 'manufacturer';
	$query_vars[] = 'pagename';
	$query_vars[] = 'mtoken';
	$query_vars[] = 'HOOK_URL';
	$query_vars[] = 'sid';
	$query_vars[] = 'buyer_cookie';
	$query_vars[] = 'cxml_from';
	$query_vars[] = 'cxml_to';

	return $query_vars;
}

add_filter('query_vars', 'propeller_query_vars');

if (! function_exists('debug_wpmail')) :
	function debug_wpmail($result = false)
	{

		if ($result) {
			return;
		}

		global $ts_mail_errors, $phpmailer;

		if (! isset($ts_mail_errors)) {
			$ts_mail_errors = array();
		}

		if (isset($phpmailer)) {
			$ts_mail_errors[] = $phpmailer->ErrorInfo;
		}

		return $ts_mail_errors;
	}
endif;
