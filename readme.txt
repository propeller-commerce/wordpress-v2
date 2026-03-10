=== Propeller Ecommerce v2 ===
Contributors: Propeller Ecommerce v2
Tags: b2b, ecommerce, propeller, graphql, wholesale, manufacturing
Requires at least: 6.2
Tested up to: 6.9
Stable tag: 1.0.0
Requires PHP: 8.1 or later
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Add a complete B2B ecommerce portal to your WordPress site powered by Propeller Commerce and GraphQL API V2.

== Description ==

**Propeller Ecommerce v2** gives your WordPress site a full B2B customer portal where your buyers can browse your catalog, see their own prices, place orders, request quotes and manage their account. Connect the plugin to your Propeller environment and you have a working storefront without building a custom frontend.

All product data, pricing rules and order processing live in Propeller. The plugin fetches everything in real time through the GraphQL API V2 so your WordPress site always shows up-to-date information. WordPress stays your CMS for pages, blog posts and content while Propeller handles the commerce.

One package, everything included. There are no paid add-ons, premium tiers or feature unlocks. Every B2B capability ships with the plugin out of the box.

= Features =

* Product catalog with filtering, sorting and multiple view modes
* Product detail pages with image galleries, pricing, specifications and downloadable files
* Cluster pages with variant selection and optional add-on products
* Shopping cart with quantity management, discount codes and full order summaries
* Checkout with address forms, payment selection, carrier selection and delivery date picking
* Customer-specific pricing with pricesheets, bulk pricing and tiered pricing tables
* User accounts with login, registration, SSO, address management and company switching
* Order history with search, filtering, return requests and reordering
* Quote request flows with accept and change request actions
* Favorite lists for saving and organizing products
* Budget limits, authorization requests and approval flows
* Configurable portal mode (Open, Semi-Closed or Closed) to control anonymous visitor access
* cXML Punchout support for B2B procurement integration
* Google Analytics 4 and Tag Manager integration
* Automatic XML sitemap generation for products, categories and brands
* Full translation management for all UI labels
* Template overrides through your theme or a custom extend plugin
* WordPress Multisite support with per-site configuration

= Portal Access Modes =

* **Open portal** (default) — Anonymous visitors can browse the catalog, see prices and place orders
* **Semi-closed portal** — Anonymous visitors can browse the catalog but must log in to see prices, stock and add products to cart
* **Closed portal** — Anonymous visitors see only the login page. All storefront content requires authentication

= Who Is This For? =

This plugin is built for B2B companies that want to offer customers a self-service ordering portal on their existing WordPress site. Ideal for manufacturers, wholesalers, distributors and any business with customer-specific catalogs or pricing.

Use the plugin to evaluate Propeller or run it in production. For projects that need full design control, most partners choose the Storefront SDK or build a custom frontend on the GraphQL API.

= Links =

* [Documentation](https://docs.propeller-commerce.com/)
* [Propeller Commerce](https://propeller-commerce.com/)
* [GitHub Repository](https://github.com/propeller-commerce/wordpress-v2)

== Installation ==

= Requirements =

* WordPress 6.2 or higher
* PHP 8.1 or higher
* An active Propeller Commerce account with two GraphQL API keys: one for general storefront operations and one for order processing

= Install from WordPress =

1. Go to Plugins > Add New in your WordPress admin
1. Search for "Propeller Ecommerce v2"
1. Click "Install Now" and then "Activate"

= Install Manually =

1. Download the plugin from the [Propeller Commerce GitHub](https://github.com/propeller-commerce/wordpress-v2)
1. Upload the plugin folder to `/wp-content/plugins/`
1. Go to Plugins in your WordPress admin and activate Propeller Ecommerce v2

= Configure the Connection =

1. Go to **Propeller > General** in your WordPress admin
1. The API URL is pre-filled with the Propeller GraphQL endpoint. Leave the default unless you use a custom environment
1. Enter your **API key** for general storefront operations (catalog browsing, authentication, product data)
1. Enter your **Orders API key** for order processing (cart mutations, placing orders)
1. Set the **Channel ID** to match your Propeller channel
1. Set the **Anonymous user ID** to control which prices anonymous visitors see
1. Set the **Catalog root ID** to define the starting category for the product tree
1. Set the **Default currency** (for example EUR, USD)
1. Click **Save settings**

= Add Header Shortcodes =

The plugin creates all commerce pages automatically but the header navigation must be added manually. Place these shortcodes in your theme header area using your page builder:

* `[menu]` — category navigation menu
* `[search]` — search bar
* `[mini-account]` — account icon and login link
* `[mini-shopping-cart]` — cart icon with item count

Without these shortcodes the commerce pages still work (accessible by URL) but the site will not have navigation, search or cart access in the header.

= Verify the Shop =

1. Visit your WordPress site frontend
1. You should see category navigation in the header
1. Navigate to a category page and confirm products from your Propeller environment appear
1. Click a product to see the detail page with pricing

If the catalog is empty, check that your API key is correct, the Channel ID matches a channel with published products and the Catalog root ID points to a category with products.

== Frequently Asked Questions ==

= Do I need a Propeller Commerce account? =

Yes. The plugin connects to the Propeller Commerce platform via GraphQL API V2. You need an active account and two valid API keys to use the plugin.

= Why are there two API keys? =

The two keys follow the principle of least privilege. The standard API key has minimum permissions for catalog browsing and general frontend operations. The orders key adds specific permissions for order processing. This separation limits the impact if a key is compromised.

= Does the plugin work with any WordPress theme? =

Yes. The commerce components work with any properly coded WordPress theme, including default themes like Twenty Twenty-Five.

= What portal access modes are available? =

Three modes. **Open** lets anonymous visitors browse, see prices and order. **Semi-closed** lets them browse the catalog but hides prices, stock and cart until they log in. **Closed** requires login before any content is visible. Open is the default.

= Can customers manage multiple companies? =

Yes. Contacts associated with multiple companies can toggle between them from the account area. Each company has its own addresses, orders, quotes and cart.

= How does pricing work? =

All pricing is managed on the Propeller backend and fetched in real time via the API. The plugin supports customer-specific pricesheets, bulk pricing, tiered pricing tables and contract rates. No pricing data is stored in WordPress.

= Can I customize the templates? =

Yes. The plugin resolves templates in priority order: theme overrides in `propeller/templates/` within your active theme, then an extend plugin directory defined via `PROPELLER_PLUGIN_EXTEND_DIR` in `wp-config.php`, then the default plugin templates. You can also use filter hooks to swap partial templates.

= Does the plugin support multiple languages? =

Yes. Set your site language via WordPress Settings > General > Site Language. Product and catalog data is fetched in that language from Propeller. For multi-language sites, the plugin works with TranslatePress (the recommended translation plugin). The plugin provides its own translation management for UI labels under Propeller > Translations.

= Does the plugin support WordPress Multisite? =

Yes. Install the plugin at the network level and configure each site independently. Each site can connect to a different Propeller channel, show a different catalog and serve a different audience. Common setups include one catalog with multiple languages, separate catalogs per site or selective commerce on only some sites in the network.

= How are sitemaps handled? =

The plugin generates XML sitemaps automatically once daily after midnight for products, categories and brands. Each sitemap type is created per language. If Yoast SEO is active, Propeller's sitemaps are included in Yoast's sitemap index instead.

= Where can I get support? =

Visit the [Propeller Documentation](https://docs.propeller-commerce.com/) or contact the Propeller support team through the [Service Desk](https://propeller-commerce.com/).

== Changelog ==

= 1.0.0 =
* First release on the WordPress plugin directory after multiple successful customer go-lives
* Full B2B portal with catalog browsing, customer-specific pricing and ordering
* Quote request and management flows
* Favorite lists and quick reordering
* Multi-company account support
* Open, Semi-Closed and Closed portal modes
* cXML Punchout support
* Google Analytics 4 and Tag Manager integration
* Automatic XML sitemap generation
* Full UI translation management
* Template override system via theme or extend plugin
* WordPress Multisite support

== Upgrade Notice ==

= 1.0.0 =
First release on the WordPress plugin directory.
