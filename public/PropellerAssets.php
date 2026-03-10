<?php

namespace Propeller\Frontend;

if ( ! defined( 'ABSPATH' ) ) exit;

use MatthiasMullie\Minify\CSS;
use MatthiasMullie\Minify\JS;
use Propeller\Propeller;

/**
 * Asset loader
 * @author Darko G.
 */
class PropellerAssets
{

	const MODE_STANDARD = 1;
	const MODE_GLOBAL_COMBINED = 2;

	/**
	 * Assets map
	 * @var array|null
	 */
	protected $assets;

	/**
	 * The asset printing mode
	 * @var integer
	 */
	protected $mode;

	/**
	 * Globally required scripts and styles
	 * @var \string[][]
	 */
	protected $global = [
		'js'  => [
			'propeller-global',
			'propeller-search',
			'propeller-menu',
			'propeller-quantity',
			'propeller-user',
			'propeller-validator',
			'propeller-cart',
			'propeller-login',
			'propeller-order-details-return'
		],
		'css' => [
			'propeller-bootstrap',
			'propeller-public',
			'propeller-responsive'
		]
	];

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->mode   = apply_filters('propel_assets_printing_mode', defined('PROPELLER_ASSETS_TYPE') ? PROPELLER_ASSETS_TYPE : self::MODE_STANDARD);
		$this->global = apply_filters('propel_global_assets', $this->global);
	}

	/**
	 * Run the scripts
	 * @return void
	 */
	public function run()
	{

		switch ($this->mode) {
			case self::MODE_STANDARD:
				$this->handle_mode_standard();
				break;
			case self::MODE_GLOBAL_COMBINED:
				$this->handle_mode_global_combined();
				break;
		}
	}

	/**
	 * Handles standard mode
	 * @return void
	 */
	private function handle_mode_standard()
	{
		add_action('wp_enqueue_scripts', [$this, 'std_register_assets'], 5);
		add_action('wp_enqueue_scripts', [$this, 'std_enqueue_global_assets'], 10);
		add_action('wp_footer', [$this, 'std_enqueue_init'], PHP_INT_MAX);
		add_action('wp_print_scripts', [$this, 'std_fix_styles'], PHP_INT_MAX - 50);
	}

	/**
	 * Is the asset loaded from cdn
	 *
	 * @param $asset
	 *
	 * @return bool
	 */
	private function is_cdn_source($asset)
	{
		return isset($asset['cdn']) && $asset['cdn'];
	}

	/**
	 * Handles the global combined mode
	 * @return void
	 */
	private function handle_mode_global_combined()
	{

		$this->load_assets();

		$js_dir           = $this->get_assets_dir('js', false) . 'src';
		$css_dir          = $this->get_assets_dir('css', false);
		$js_dir_extend    = $this->get_assets_dir('js', true) . 'src';
		$css_dir_extend   = $this->get_assets_dir('css', true);
		$js_dir_last_mod  = file_exists($js_dir_extend) ? filemtime($js_dir_extend) : filemtime($js_dir);
		$css_dir_last_mod = file_exists($css_dir_extend) ? filemtime($css_dir_extend) : filemtime($css_dir);
		$js_db_last_mod   = (int) get_transient(PROPELLER_RESOURCE_LAST_MOD . '_js');
		$css_db_last_mod  = (int) get_transient(PROPELLER_RESOURCE_LAST_MOD . '_css');
		$js_path          = $this->get_assets_dir('js') . 'propel.min.js';
		$css_path         = $this->get_assets_dir('css') . 'propel.min.css';
		$js_needs_minify  = ($js_db_last_mod != $js_dir_last_mod) || ! file_exists($js_path);
		$css_needs_minify = ($css_db_last_mod != $css_dir_last_mod) || ! file_exists($css_path);

		if ($js_needs_minify || $css_needs_minify) {

			clearstatcache(true);

			$js_minifier  = new JS();
			$css_minifier = new CSS();

			$minify_js  = false;
			$minify_css = false;

			foreach ($this->assets as $type => $list) {
				foreach ($list as $handle => $asset) {
					if ($js_needs_minify) {
						if (! empty($asset['js']['src']) && ! $this->is_cdn_source($asset['js']) && (! isset($asset['js']['condition']) || $asset['js']['condition'])) {
							$details = $this->get_asset_src('js', $asset['js']['src'], 'external' === $type);
							$js_minifier->add($details['file_path']);
							$minify_js = true;
						}
					}
					if ($css_needs_minify) {
						if (! empty($asset['css']['src']) && ! $this->is_cdn_source($asset['css']) && (! isset($asset['js']['condition']) || $asset['js']['condition'])) {
							$details = $this->get_asset_src('css', $asset['css']['src'], 'external' === $type);
							$css_minifier->add($details['file_path']);
							$minify_css = true;
						}
					}
				}
			}
			if ($minify_js) {
				$js_minifier->minify($js_path);
				$js_dir_last_mod = file_exists($js_dir_extend) ? filemtime($js_dir_extend) : filemtime($js_dir);
				set_transient(PROPELLER_RESOURCE_LAST_MOD . '_js', $js_dir_last_mod);
			}
			if ($minify_css) {
				$css_minifier->minify($css_path);
				$css_dir_last_mod = file_exists($css_dir_extend) ? filemtime($css_dir_extend) : filemtime($css_dir);
				set_transient(PROPELLER_RESOURCE_LAST_MOD . '_css', $css_dir_last_mod);
			}
		}

		add_action('wp_enqueue_scripts', function () {
			// handle cdn assets
			foreach ($this->assets as $type => $list) {
				foreach ($list as $handle => $asset) {
					if (isset($asset['js']) && $this->is_cdn_source($asset['js']) && (! isset($asset['js']['condition']) || $asset['js']['condition'])) {
						wp_enqueue_script($handle, $asset['js']['src'], [], null, true);
					}
					if (isset($asset['css']) && $this->is_cdn_source($asset['css']) && (! isset($asset['js']['condition']) || $asset['js']['condition'])) {
						wp_enqueue_style($handle, $asset['css']['src'], [], null, 'all');
					}
				}
			}
			wp_enqueue_script('propeller_js', $this->get_assets_url('js') . 'propel.min.js', array(
				'jquery',
				'wp-i18n'
			), PROPELLER_VERSION, true);
			wp_enqueue_style('propeller_css', $this->get_assets_url('css') . 'propel.min.css', array(), null, 'all');
		});
	}

	/**
	 * Enqueues asset script
	 *
	 * @param string|array $handle
	 *
	 * @return void
	 */
	public function std_requires_asset($handle)
	{

		if (self::MODE_STANDARD !== $this->mode) {
			return;
		}


		foreach ((array) $handle as $single_handle) {
			wp_enqueue_script($single_handle);
			wp_enqueue_style($single_handle);

			$deps = $this->std_find_deps($single_handle);
			foreach ($deps as $dep) {
				wp_enqueue_style($dep);
			}
		}
	}

	/**
	 * Find propeller external CSS dependencies based on the Javascript handle that is enqueued.
	 *
	 * @param $handle
	 *
	 * @return array
	 */
	public function std_find_deps($handle)
	{
		$this->load_assets();
		$deps = ! empty($this->assets['internal'][$handle]['js']['deps']) ? $this->assets['internal'][$handle]['js']['deps'] : [];
		$list = [];
		foreach ($deps as $dep) {
			if (isset($this->assets['internal'][$dep])) {
				$new_deps = $this->std_find_deps($dep);
				if (! is_null($new_deps)) {
					$list = array_merge($list, $new_deps);
				}
			} else {
				$list[] = isset($this->assets['external'][$dep]['css']) ? $dep : null;
			}
		}

		return $list;
	}

	/**
	 * Registers the assets map
	 * @return void
	 */
	public function std_register_assets()
	{

		$this->load_assets();

		// External assets
		foreach ($this->assets['external'] as $handle => $asset) {
			$this->std_register_asset($handle, $asset, true);
		}

		// Internal assets
		foreach ($this->assets['internal'] as $handle => $asset) {
			$this->std_register_asset($handle, $asset, false);
		}
	}

	/**
	 * Enqueue global assets.
	 *
	 * @return void
	 */
	public function std_enqueue_global_assets()
	{
		foreach ($this->global['css'] as $global_style) {
			wp_enqueue_style($global_style);
		}
		foreach ($this->global['js'] as $global_script) {
			wp_enqueue_script($global_script);
		}

		if (defined('PROPELLER_PLUGIN_EXTEND_DIR')) {
			$languages_path = PROPELLER_PLUGIN_EXTEND_DIR . DIRECTORY_SEPARATOR . 'languages' . DIRECTORY_SEPARATOR;
		} else {
			$languages_path = PROPELLER_PLUGIN_DIR . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'languages' . DIRECTORY_SEPARATOR;
		}
		wp_set_script_translations('propeller-global', 'propeller-ecommerce-v2', $languages_path);
	}

	/**
	 * Enqueues the init script as last of the last
	 * @return void
	 */
	public function std_enqueue_init()
	{
		$dir = $this->get_assets_dir('js', false);
		echo "<script>";
		include $dir . 'src/init.js';
		echo "</script>";
	}

	/**
	 * Automatically enqueue styles if a script is enqueued with same handle as existing style.
	 */
	public function std_fix_styles()
	{

		$this->load_assets();

		foreach ($this->assets['external'] as $handle => $asset) {
			if (isset($asset['css']) && isset($asset['js'])) {
				if (wp_script_is($handle)) {
					wp_enqueue_style($handle);
				}
			}
		}
	}

	/**
	 * Register single asset
	 *
	 * @param $handle
	 * @param $asset
	 *
	 * @return void
	 */
	public function std_register_asset($handle, $asset, $external = false)
	{
		if (isset($asset['js'])) {
			if ($this->is_cdn_source($asset['js'])) {
				$deps      = isset($asset['js']['deps']) ? $asset['js']['deps'] : [];
				$in_footer = isset($asset['js']['in_footer']) ? $asset['js']['in_footer'] : true;
				$version   = null; // isset( $asset['js']['version'] ) ? $asset['js']['version'] : false;
				wp_register_script($handle, $asset['js']['src'], $deps, $version, $in_footer,);
			} else {
				$paths     = $this->get_asset_src('js', $asset['js']['src'], $external);
				$deps      = isset($asset['js']['deps']) ? $asset['js']['deps'] : [];
				$version   = null; // isset( $asset['js']['version'] ) ? $asset['js']['version'] : ( defined( 'WP_DEBUG' ) && WP_DEBUG ? filemtime( $paths['file_path'] ) : false );
				$in_footer = isset($asset['js']['in_footer']) ? $asset['js']['in_footer'] : true;
				wp_register_script($handle, $paths['file_url'], $deps, $version, $in_footer);
			}
		}
		if (isset($asset['css'])) {
			if ($this->is_cdn_source($asset['css'])) {
				$deps    = isset($asset['css']['deps']) ? $asset['css']['deps'] : [];
				$version = isset($asset['css']['version']) ? $asset['css']['version'] : false;
				$media   = isset($asset['css']['media']) ? $asset['css']['media'] : 'all';
				wp_register_style($handle, $asset['css']['src'], $deps, $version, $media);
			} else {
				$paths   = $this->get_asset_src('css', $asset['css']['src'], $external);
				$deps    = isset($asset['css']['deps']) ? $asset['css']['deps'] : [];
				$version = isset($asset['css']['version']) ? $asset['css']['version'] : (defined('WP_DEBUG') && WP_DEBUG ? filemtime($paths['file_path']) : false);
				$media   = isset($asset['css']['media']) ? $asset['css']['media'] : 'all';
				wp_register_style($handle, $paths['file_url'], $deps, $version, $media);
			}
		}
	}

	/**
	 * Return assets dir path
	 *
	 * @param $type
	 * @param bool $extend
	 *
	 * @return string
	 */
	public function get_assets_dir($type, $extend = false)
	{

		if ($extend) {
			if (! defined('PROPELLER_PLUGIN_EXTEND_DIR')) {
				return null;
			}

			return trailingslashit(PROPELLER_PLUGIN_EXTEND_DIR) . 'public' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . $type . DIRECTORY_SEPARATOR;
		}

		return trailingslashit(PROPELLER_PLUGIN_DIR) . 'public' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . $type . DIRECTORY_SEPARATOR;
	}

	/**
	 * Returns assets dir url
	 *
	 * @param $type
	 * @param bool $extend
	 *
	 * @return string
	 */
	public function get_assets_url($type, $extend = false)
	{
		if ($extend) {
			return PROPELLER_PLUGIN_EXTEND_URL . '/public/assets/' . $type . '/';
		}

		return PROPELLER_PLUGIN_DIR_URL . 'public/assets/' . $type . '/';
	}

	/**
	 * Returns the extended script
	 *
	 * @param $type
	 * @param $path
	 * @param bool $external
	 *
	 * @return array
	 */
	public function get_asset_src($type, $path, $external = false)
	{
		static $cached = [];
		$path      = ltrim($path, '/');
		$cache_key = $type . '/' . $path;
		if (isset($cached[$cache_key])) {
			return $cached[$cache_key];
		} else {
			$is_extend = false;
			$url_path  = str_replace(DIRECTORY_SEPARATOR, '/', $path);
			$file_url  = $this->get_assets_url($type, false) . $url_path;
			$file_path = $this->get_assets_dir($type, false) . $path;
			if (! $external) {
				$extends_dir = $this->get_assets_dir($type, true);
				if (! is_null($extends_dir) && ! isset($cached['extend_dir_exists_' . $type])) {
					$cached['extend_dir_exists_' . $type] = file_exists($extends_dir);
				}
				if (isset($cached['extend_dir_exists_' . $type]) && $cached['extend_dir_exists_' . $type]) {
					if (file_exists($extends_dir . $path)) {
						$file_path = $extends_dir . $path;
						$file_url  = $this->get_assets_url($type, true) . $url_path;
						$is_extend = true;
					}
				}
			}
			$cached[$cache_key] = ['file_path' => $file_path, 'file_url' => $file_url, 'extend' => $is_extend];

			return $cached[$cache_key];
		}
	}

	/**
	 * Map of scripts required for the site to run
	 *
	 * Note: 'condition' paraemter is only used for global/minified assets.
	 *
	 * Warning: Please update this map once you add/remove dependencies in specific script.
	 *
	 * @return \array[][][]
	 */
	public function get_assets_map()
	{
		$lang = defined('PROPELLER_LANG') ? strtolower(PROPELLER_LANG) : PROPELLER_FALLBACK_LANG;

		$map = apply_filters('propel_frontend_assets', [
			'external' => [

				'propeller-recaptcha' => [
					'js' => [
						'src'       => 'https://www.google.com/recaptcha/api.js?render=' . (Propeller::use_recaptcha() && defined('PROPELLER_RECAPTCHA_SITEKEY') ? PROPELLER_RECAPTCHA_SITEKEY : '') . '&lang=' . $lang,
						'cdn'       => true,
						'condition' => Propeller::use_recaptcha() // only used for global/minified mode.
					]
				],

				'propeller-autocomplete' => [
					'js'  => [
						'src'  => 'lib/autoComplete.min.js',
						'deps' => [],
					],
					'css' => [
						'src'  => 'lib/autoComplete.min.css',
						'deps' => [],
					]
				],


				'propeller-jquery-validate' => [
					'js' => [
						'src'  => 'lib/jquery-validator/jquery.validate.js',
						'deps' => ['jquery'],
					]
				],

				'propeller-list'       => [
					'js'  => [
						'src'  => 'lib/list.min.js',
						'deps' => [],
					]
				],

				'propeller-jquery-validate-additions' => [
					'js' => [
						'src'  => 'lib/jquery-validator/additional-methods.js',
						'deps' => ['propeller-jquery-validate'],
					]
				],

				'propeller-nouislider'       => [
					'js'  => [
						'src'  => 'lib/nouislider.min.js',
						'deps' => [],
					],
					'css' => [
						'src'  => 'lib/nouislider.min.css',
						'deps' => [],
					]
				],
				'propeller-popper'           => [
					'js' => [
						'src'  => 'lib/popper.min.js',
						'deps' => ['jquery'],
					]
				],
				'propeller-bootstrap'        => [
					'js'  => [
						'src'  => 'lib/bootstrap.min.js',
						'deps' => [],
					],
					'css' => [
						'src'  => 'lib/bootstrap.min.css',
						'deps' => [],
					]
				],
				'propeller-cookie'           => [
					'js'  => [
						'src'  => 'lib/js-cookie.min.js',
						'deps' => [],
					]
				],
				'propeller-cookie-compat'    => [
					'js'  => [
						'src'  => 'lib/jquery-cookie-compat.js',
						'deps' => ['jquery', 'propeller-cookie'],
					]
				],
				'propeller-plain-overlay'    => [
					'js' => [
						'src'  => 'lib/plain-overlay.min.js',
						'deps' => [],
					]
				],
				'propeller-calendar-library' => [
					'js'  => [
						'src'  => 'lib/calendar.min.js',
						'deps' => [],
					],
					'css' => [
						'src'  => 'lib/calendartheme.css',
						'deps' => [],
					]
				],
				'propeller-slick'            => [
					'js'  => [
						'src'  => 'lib/slick.min.js',
						'deps' => ['jquery'],
					],
					'css' => [
						'src'  => 'lib/slick-combined.css',
						'deps' => [],
					]
				],
				'propeller-droprown'            => [
					'js'  => [
						'src'  => 'lib/tom-select.complete.js',
						'deps' => ['jquery'],
					],
					'css' => [
						'src'  => 'lib/tom-select.default.min.css',
						'deps' => [],
					]
				],
				'propeller-photoswipe'       => [
					'js'  => [
						'src'  => 'lib/photoswipe-4.1.3.min.js',
						'deps' => [],
					],
					'css' => [
						'src'  => 'lib/photoswipe.css',
						'deps' => [],
					]
				],
				'propeller-photoswipe-ui'     => [
					'js'  => [
						'src'  => 'lib/photoswipe-ui-default-4.1.3.min.js',
						'deps' => ['propeller-photoswipe'],
					]
				],
				'propeller-lazyload'       => [
					'js'  => [
						'src'  => 'lib/lazyload.min.js',
						'deps' => [],
					]
				],
			],
			'internal' => [
				'propeller-global'            => [
					'js' => [
						'src'  => 'src/global.js',
						'deps' => ['jquery'],
					]
				],
				'propeller-account-paginator' => [
					'js' => [
						'src'  => 'src/account-paginator.js',
						'deps' => [
							'propeller-global',
							'propeller-ajax'
						],
					]
				],
				'propeller-action-tooltip'    => [
					'js' => [
						'src'  => 'src/action-tooltip.js',
						'deps' => [
							'propeller-global',
							'propeller-bootstrap'
						],
					]
				],
				'propeller-address-default'   => [
					'js' => [
						'src'  => 'src/address-default.js',
						'deps' => [
							'propeller-global',
							'propeller-ajax',
							'propeller-toast',
							'wp-i18n'
						],
					]
				],
				'propeller-ajax'              => [
					'js' => [
						'src'  => 'src/ajax.js',
						'deps' => [
							'propeller-global',
							'propeller-plain-overlay',
							'propeller-lazyload'
						],
					]
				],
				'propeller-bulk-prices'       => [
					'js' => [
						'src'  => 'src/bulk-prices.js',
						'deps' => [
							'propeller-global'
						],
					]
				],
				'propeller-calendar'          => [
					'js' => [
						'src'  => 'src/calendar.js',
						'deps' => [
							'propeller-global',
							'propeller-bootstrap',
							'propeller-calendar-library',
							'wp-i18n'
						],
					]
				],
				'propeller-cart'              => [
					'js' => [
						'src'  => 'src/cart.js',
						'deps' => [
							'propeller-global',
							'propeller-modal',
							'propeller-ajax',
							'wp-i18n'
						],
					]
				],

				'propeller-catalog'        => [
					'js' => [
						'src'  => 'src/catalog.js',
						'deps' => [
							'propeller-global'
						],
					]
				],
				'propeller-machines'        => [
					'js' => [
						'src'  => 'src/catalog.js',
						'deps' => [
							'propeller-global'
						],
					]
				],
				'propeller-checkout-forms' => [
					'js' => [
						'src'  => 'src/checkout-forms.js',
						'deps' => [
							'propeller-global',
						],
					]
				],
				'propeller-checkout'       => [
					'js' => [
						'src'  => 'src/checkout.js',
						'deps' => [
							'propeller-global',
							'propeller-toast',
							'propeller-checkout-forms',
							'propeller-calendar',
							'wp-i18n'
						],
					]
				],
				'propeller-cross-upsells'  => [
					'js' => [
						'src'  => 'src/cross-upsells.js',
						'deps' => [
							'propeller-global',
							'propeller-ajax',
							'propeller-quantity',
							'propeller-product',
							'propeller-slick',
						],
					]
				],

				'propeller-filters' => [
					'js' => [
						'src'  => 'src/filters.js',
						'deps' => [
							'propeller-global',
							'propeller-offcanvas',
							'propeller-ajax',
							'propeller-frontend',
							'propeller-catalog',
							'propeller-machines',
							'propeller-nouislider'
						],
					]
				],

				'propeller-form-return-products' => [
					'js' => [
						'src'  => 'src/form-return-products.js',
						'deps' => [
							'propeller-global',
						],
					]
				],

				'propeller-gallery' => [
					'js' => [
						'src'  => 'src/gallery.js',
						'deps' => [
							'propeller-global',
							'propeller-slick',
						],
					]
				],

				'propeller-gallery-item' => [
					'js' => [
						'src'  => 'src/gallery-item.js',
						'deps' => [
							'propeller-global',
							'propeller-photoswipe',
							'propeller-photoswipe-ui',
						],
					]
				],

				'propeller-login' => [
					'js' => [
						'src'  => 'src/login.js',
						'deps' => [
							'propeller-global',
							'propeller-toast',
							'wp-i18n',
						],
					]
				],

				'propeller-menu' => [
					'js' => [
						'src'  => 'src/menu.js',
						'deps' => [
							'propeller-global',
						]
					]
				],

				'propeller-modal' => [
					'js' => [
						'src'  => 'src/modal.js',
						'deps' => [
							'propeller-global',
							'propeller-bootstrap',
						]
					]
				],

				'propeller-offcanvas' => [
					'js' => [
						'src'  => 'src/offcanvas.js',
						'deps' => [
							'propeller-global',
						]
					]
				],

				'propeller-order-details-return' => [
					'js' => [
						'src'  => 'src/order-details-return.js',
						'deps' => [
							'propeller-global',
							'propeller-toast',
							'propeller-bootstrap',
							'wp-i18n',
						]
					]
				],

				'propeller-paginator' => [
					'js' => [
						'src'  => 'src/paginator.js',
						'deps' => [
							'propeller-global',
							'propeller-catalog',
							'propeller-machines',
							'propeller-ajax'
						]
					]
				],

				'propeller-product'               => [
					'js' => [
						'src'  => 'src/product.js',
						'deps' => [
							'propeller-global',
							'propeller-ajax',
							'propeller-quantity',
							'propeller-product-fixed-wrapper',
							'propeller-photoswipe',
							'propeller-slick',
							'propeller-droprown'
						]
					]
				],
				'propeller-product-fixed-wrapper' => [
					'js' => [
						'src'  => 'src/product-fixed-wrapper.js',
						'deps' => [
							'propeller-global',
							'propeller-ajax',
						]
					]
				],

				'propeller-quantity' => [
					'js' => [
						'src'  => 'src/quantity.js',
						'deps' => [
							'propeller-global',
						]
					]
				],

				'propeller-quickorder' => [
					'js' => [
						'src'  => 'src/quickorder.js',
						'deps' => [
							'propeller-global'
						]
					]
				],

				'propeller-register' => [
					'js' => [
						'src'  => 'src/register.js',
						'deps' => [
							'propeller-global',
							'propeller-toast',
							'wp-i18n',
						]
					]
				],

				'propeller-frontend' => [
					'js' => [
						'src'  => 'src/frontend.js',
						'deps' => [
							'propeller-global',
							'propeller-cart',
							'propeller-paginator',
							'propeller-menu'
						],
					]
				],

				'propeller-search' => [
					'js' => [
						'src'  => 'src/search.js',
						'deps' => [
							'propeller-global',
							'propeller-autocomplete',
							'propeller-ajax',
							'wp-i18n',
							'propeller-list'
						]
					]
				],

				'propeller-slider' => [
					'js' => [
						'src'  => 'src/slider.js',
						'deps' => [
							'propeller-global',
							'propeller-slick',
							'propeller-product',
						]
					]
				],

				'propeller-slider-recently-viewed' => [
					'js' => [
						'src'  => 'src/slider-recently-viewed.js',
						'deps' => [
							'propeller-global',
							'propeller-slick',
							'propeller-ajax',
							'propeller-product',
						]
					]
				],

				'propeller-toast' => [
					'js' => [
						'src'  => 'src/toast.js',
						'deps' => [
							'propeller-global',
							'propeller-bootstrap',
						]
					]
				],

				'propeller-truncate-content' => [
					'js' => [
						'src'  => 'src/truncate-content.js',
						'deps' => [
							'propeller-global',
						]
					]
				],

				'propeller-user' => [
					'js' => [
						'src'  => 'src/user.js',
						'deps' => [
							'propeller-global',
							'propeller-ajax',

						]
					]
				],

				'propeller-validator' => [
					'js' => [
						'src'  => 'src/validator.js',
						'deps' => [
							'propeller-global',
							'propeller-jquery-validate',
							'propeller-jquery-validate-additions',
							'propeller-bootstrap',
							'propeller-ajax',
							'propeller-toast',
							'wp-i18n',
							'propeller-list'
						]
					]
				],

				'propeller-pricerequest' => [
					'js' => [
						'src'  => 'src/price-request.js',
						'deps' => [
							'propeller-product'
						]
					]
				],

				'propeller-spareparts' => [
					'js' => [
						'src'  => 'src/spare-parts.js',
						'deps' => []
					]
				],

				'propeller-favorites' => [
					'js' => [
						'src'  => 'src/favorite.js',
						'deps' => [
							'propeller-list'
						]
					]
				],

				'propeller-public' => [
					'css' => [
						'src'  => 'propeller-frontend.css',
						'deps' => [],
					]
				],

				'propeller-responsive' => [
					'css' => [
						'src'  => 'propeller-responsive.css',
						'deps' => [],
					]
				]
			]
		]);

		// If recaptcha is used.
		if (Propeller::use_recaptcha()) {
			$map['internal']['propeller-validator']['js']['deps'][] = 'propeller-recaptcha';
		}

		// This has to be the last piece used for initialization.
		$map['internal']['propeller-init'] = [
			'js' => [
				'src'  => 'src/init.js',
				'deps' => [
					'jquery',
					'propeller-global',
				],
			]
		];

		return $map;
	}


	/**
	 * Lazily loads the assets map
	 * @return void
	 */
	private function load_assets()
	{
		if (! is_null($this->assets)) {
			return;
		}
		$this->assets = $this->get_assets_map();
	}
}
