<?php

namespace Propeller;

if ( ! defined( 'ABSPATH' ) ) exit;

use voku\helper\AntiXSS;


class PropellerUtils
{

	/**
	 * Sanitizes input data array
	 * @param $data
	 *
	 * @return mixed
	 */
	public static function sanitize($data)
	{
		$xss = new AntiXSS();

		foreach ($data as $key => $value) {
			if (is_array($value))
				$data[$key] = self::sanitize($data[$key]);
			else {
				if (is_numeric($data[$key])) {
					if (substr($data[$key], 0, 1) != '0') {
						if (strpos($data[$key], '.') !== false) {
							$data[$key] = (float) $xss->xss_clean(rawurldecode($data[$key]));
						} else {
							$data[$key] = (int) $xss->xss_clean(rawurldecode($data[$key]));
						}
					}
				} elseif (is_string($data[$key])) {
					$value = wp_unslash(rawurldecode($data[$key]));

					if ($value == 'true' || $value == 'false')
						$data[$key] = boolval($xss->xss_clean($value));
					else {
						if (stripos($key, 'password') !== false) {
							$data[$key] = $xss->xss_clean($value);
							continue;
						}
						if (stripos($key, 'cxml_contact_id') !== false) {
							$data[$key] = $xss->xss_clean($value);
							continue;
						}

						if (substr($value, 0, 1) != '0') {
							if (stripos($key, 'mail') !== false) {
								$data[$key] = sanitize_email($xss->xss_clean($value));
							} else {
								if (stripos($key, 'manufacturers') !== false) {
									$data[$key] = sanitize_text_field($xss->xss_clean($value));
									$data[$key] = stripslashes($data[$key]);
								} else {
									$data[$key] = sanitize_text_field($xss->xss_clean($value));
								}
							}
						}
					}
				} elseif (is_bool($data[$key])) {
					$data[$key] = boolval($xss->xss_clean(rawurldecode($data[$key])));
				}
			}
		}

		return $data;
	}

	/**
	 * Sanitizes query strings, eg:
	 *
	 * eg input: t=234328942&test=2384238423
	 *
	 * http_build_query will use urlencode on the properties.
	 *
	 * @param $query_string
	 *
	 * @return string
	 */
	public static function sanitize_query_string($query_string)
	{
		parse_str($query_string, $vars);
		return http_build_query($vars);
	}
}
