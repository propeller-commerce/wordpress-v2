<?php

declare(strict_types=1);

namespace Propeller\Includes\Object;

if (! defined('ABSPATH')) exit;

use Propeller\Includes\Enum\ProductStatus;
use stdClass;

/**
 * @property string $class Product/Cluster class identifier from the API
 */
class Cluster extends BaseObject
{
    const CLUSTER_TYPE_NORMAL = 'normal';
    const CLUSTER_TYPE_LINEAR = 'linear';
    const CLUSTER_TYPE_CONFIGURABLE = 'configurable';

    const CLUSTER_DISPLAY_OPTIONS_RADIO = 'RADIO';
    const CLUSTER_DISPLAY_OPTIONS_DROPDOWN = 'DROPDOWN';
    const CLUSTER_DISPLAY_OPTIONS_COLOR = 'COLOR';
    const CLUSTER_DISPLAY_OPTIONS_IMAGE = 'IMAGE';

    public array $selected_options = [];

    public mixed $cluster_type = null;
    public mixed $display_options = null;
    public array $config_options = [];

    public mixed $defaultProduct = null;
    public mixed $config = null;
    public array $options = [];
    public array $products = [];
    public mixed $crossupsells;
    public ?stdClass $trackAttributes = null;
    public ?stdClass $trackAttributeItems = null;

    public array $name = [];
    public array $slug = [];

    // public array $name = [];
    // public array $slug = [];

    public function __construct(object $cluster, bool $initialize = true)
    {
        parent::__construct($cluster);

        $request_data = $this->sanitize($_REQUEST);

        $this->purge_cluster();

        $this->trackAttributeItems = new stdClass();
        $this->trackAttributeItems->itemsFound = 0;

        // sort config attributes by priority
        if (isset($this->config) && isset($this->config->settings) && count($this->config->settings))
            $this->config->settings = $this->sort_config($this->config->settings);

        if (isset($this->options) && is_array($this->options) && count($this->options)) {
            $option_arr = [];

            foreach ($this->options as $option) {
                $options_product_arr = [];

                foreach ($option->products as $product) {
                    $options_product_arr[] = new Product($product);

                    if ($product->productId == $option->defaultProduct->productId && !isset($option->defaultProduct->name))
                        $option->defaultProduct = new Product($product);
                }

                $option->products = $options_product_arr;

                $option_arr[] = $option;
            }

            $this->options = $option_arr;
        }

        if (isset($this->products) && is_array($this->products) && count($this->products)) {
            $product_arr = [];

            foreach ($this->products as $product) {
                $product_arr[] = new Product($product);

                // set option default product
                if (isset($this->defaultProduct) && !isset($this->defaultProduct->name) && $this->defaultProduct->productId == $product->productId)
                    $this->defaultProduct = $product;
            }

            $this->products = $product_arr;
        }



        if (isset($cluster->trackAttributes) && isset($cluster->trackAttributes->itemsFound) && $cluster->trackAttributes->itemsFound > 0) {
            $attrs = new AttributeArray($cluster->trackAttributes);
            $this->trackAttributes = $attrs->get_non_empty_attrs();

            $this->trackAttributeItems->itemsFound = $cluster->trackAttributes->itemsFound;
            $this->trackAttributeItems->offset = $cluster->trackAttributes->offset;
            $this->trackAttributeItems->page = $cluster->trackAttributes->page;
            $this->trackAttributeItems->pages = $cluster->trackAttributes->pages;
            $this->trackAttributeItems->start = $cluster->trackAttributes->start;
            $this->trackAttributeItems->end = $cluster->trackAttributes->end;
        }

        if (isset($this->defaultProduct))
            $this->defaultProduct = new Product($this->defaultProduct);

        if ($initialize && $this->config && count($this->config->settings))
            $this->init_cluster($request_data);
    }

    public function purge_cluster(): void
    {
        if (is_null($this->products))
            return;

        $unset_products = [];

        $index = 0;

        $total_products = count($this->products);

        foreach ($this->products as $product) {
            if ($product->status == ProductStatus::N) {
                $unset_products[] = $product->productId;
                unset($this->products[$index]);
            }

            $index++;
        }

        // if all products are not available, go to 404
        if (count($unset_products) > 0 && count($unset_products) == $total_products)
            $this->defaultProduct = null;
        else {
            // set the first product found that is available and it's not the default one as the default cluster product
            if (isset($this->defaultProduct) && !is_null($this->defaultProduct) && in_array($this->defaultProduct->productId, $unset_products)) {
                $next_default_found = array_filter($this->products, function ($product) {
                    return $product->status != ProductStatus::N;
                });

                if (count($next_default_found)) {
                    $reverse = array_reverse($next_default_found);
                    $next_default = array_pop($reverse);

                    $this->defaultProduct->productId = $next_default->productId;
                }
            }
        }
    }

    private function sort_config(array $settings): array
    {
        usort($settings, function ($a, $b) {
            if ($a->priority == '0') $a->priority = 0;
            else $a->priority = (int) $a->priority;

            if ($b->priority == '0') $b->priority = 0;
            else $b->priority = (int) $b->priority;

            return $a->priority > $b->priority ? 1 : -1;
        });

        return $settings;
    }

    public function init_cluster(array $request_data): void
    {
        $this->config_options = [];

        foreach ($this->config->settings as $setting) {
            $config = new stdClass();
            $config->setting_name = $setting->name;
            $config->type = $setting->displayType;
            $config->options = [];

            foreach ($this->get_products() as $product) {
                if (!isset($config->name)) {
                    $attr = $product->get_attr_by_name($setting->name);

                    if ($attr)
                        $config->name = esc_html($attr->get_description());
                }

                $val = $product->get_attr_value($setting->name);

                $found = array_filter($config->options, function ($conf) use ($val) {
                    return $conf->value == $val;
                });

                if (!count($found)) {
                    $value = new stdClass();
                    $value->value = $val;
                    $value->disabled = '';

                    $config->options[] = $value;
                }
            }

            $config->selected = isset($request_data[$config->setting_name]) ? $request_data[$config->setting_name] : '';

            $this->config_options[] = $config;
        }

        // select the first option in the highest prio options
        if (empty($this->config_options[0]->selected)) {
            if (isset($request_data[$this->config_options[0]->setting_name])) // if is set via request
                $this->config_options[0]->selected = $request_data[$this->config_options[0]->setting_name];
            else  // always fallback to the default product leader value
                $this->config_options[0]->selected = $this->defaultProduct->get_attr_value($this->config_options[0]->setting_name);
        }

        $leader_name = $this->config_options[0]->setting_name;
        $leader_value = $this->config_options[0]->selected;

        // reset everything if leader is selected
        if (isset($request_data['clicked_attr']) && $request_data['clicked_attr'] == $leader_name) {
            foreach ($this->config_options as $config_opt) {
                if ($config_opt->setting_name == $leader_name)
                    continue;

                $config_opt->selected = '';

                foreach ($config_opt->options as $opt)
                    $opt->disabled = '';

                if (isset($request_data[$config_opt->setting_name]))
                    unset($request_data[$config_opt->setting_name]);
            }
        }

        // match products based on the leader value
        $leader_matched_products = array_filter($this->get_products(), function ($product) use ($leader_name, $leader_value) {
            return $product->get_attr_value($leader_name) == $leader_value;
        });


        $enabled_opts = [
            $leader_value => []
        ];

        // get all options that should be enabled based on the leader option
        foreach ($leader_matched_products as $product) {
            $index = 0;

            foreach ($this->config_options as $config_opt) {
                if ($index > 0) {
                    if (!isset($enabled_opts[$leader_value][$config_opt->setting_name]))
                        $enabled_opts[$leader_value][$config_opt->setting_name] = [];

                    $enabled_opts[$leader_value][$config_opt->setting_name][] = $product->get_attr_value($config_opt->setting_name);
                }

                $index++;
            }
        }

        // make arrays of available options unique
        foreach ($this->config_options as $config_opt) {
            if (isset($enabled_opts[$leader_value][$config_opt->setting_name]) && is_array($enabled_opts[$leader_value][$config_opt->setting_name]))
                $enabled_opts[$leader_value][$config_opt->setting_name] = array_unique($enabled_opts[$leader_value][$config_opt->setting_name]);
        }

        // var_dump($enabled_opts);

        $index = 0;

        // disable all options that are not in the available product options based on the leader
        foreach ($this->config_options as $config_opt) {
            if ($index > 0) {
                foreach ($config_opt->options as $opt) {
                    if (!isset($enabled_opts[$leader_value][$config_opt->setting_name]) || !in_array($opt->value, $enabled_opts[$leader_value][$config_opt->setting_name]))
                        $opt->disabled = 'disabled';
                }
            }

            $index++;
        }

        // Products that are matched via other attrs beside the leader
        $click_matched_products = [];
        $has_clicked_attr = false;

        if (isset($request_data['clicked_attr']) && $request_data['clicked_attr'] != $leader_name) {
            $has_clicked_attr = true;

            $attr_name = $request_data['clicked_attr'];
            $attr_value = $request_data['clicked_val'];

            $click_matched_products = array_filter($leader_matched_products, function ($product) use ($attr_name, $attr_value) {
                return $product->get_attr_value($attr_name) == $attr_value;
            });

            $match_attr = array_filter($this->config_options, function ($conf) use ($attr_name) {
                return $conf->setting_name == $attr_name;
            });

            current($match_attr)->selected = $attr_value;

            if (count($click_matched_products) == 1) {
                $index = 0;
                foreach ($this->config_options as $config_opt) {
                    if ($index > 0 && $config_opt->setting_name != $attr_name) {
                        $config_opt->selected = current($click_matched_products)->get_attr_value($config_opt->setting_name);

                        // var_dump($config_opt->selected);
                        foreach ($config_opt->options as $opt) {
                            if ($opt->value != $config_opt->selected)
                                $opt->disabled = 'disabled';
                        }
                    }

                    $index++;
                }
            }
        }

        $count_matches = array_count_values(array_column($leader_matched_products, 'productId'));

        // if there is only one product, then select it's config options
        if (count($leader_matched_products) == 1) {
            foreach ($this->config_options as $config_opt) {
                if ($config_opt->setting_name == $leader_name)
                    continue;

                $conf_name = $config_opt->setting_name;

                $conf_val = !empty($config_opt->selected) ? $config_opt->selected : $product->get_attr_value($config_opt->setting_name);

                $config_opt->selected = $conf_val;

                foreach ($config_opt->options as $opt) {
                    if ($opt->value != $config_opt->selected)
                        $opt->disabled = 'disabled';
                }
            }
        } else {
            if (count($click_matched_products) != 1) {
                $index = 0;
                $matches = [];

                // disable all options that are not in the available product options
                foreach ($this->config_options as $config_opt) {
                    if ($index > 0) {
                        if ($has_clicked_attr && $config_opt->setting_name == $request_data['clicked_attr'])
                            continue;

                        $config_opt->selected = '';
                        // var_dump("global: $index");

                        $matches[$index] = [];

                        $products_matches = isset($matches[$index - 1]) ? $matches[$index - 1] : $leader_matched_products;
                        $count_matches = array_count_values(array_column($products_matches, 'productId'));
                        $matched_products_num = count($count_matches);

                        $conf_name = $config_opt->setting_name;

                        // var_dump($conf_name);
                        // var_dump($count_matches);
                        // var_dump($matched_products_num);

                        // var_dump("inner matched products num: ". $matched_products_num);

                        /*if (!empty($config_opt->selected)) {
                            // var_dump("has selected $config_opt->selected");
                            $opt_val = $config_opt->selected;

                            $match = array_filter($products_matches, function($product) use($conf_name, $opt_val){
                                return $product->get_attr_value($conf_name) == $opt_val;
                            });

                            if (count($match)) {
                                foreach ($match as $prod)
                                    $matches[$index][] = $prod;

                                foreach ($config_opt->options as $opt) {
                                    if (empty($opt->disabled) && $matched_products_num == 1)
                                            $opt->disabled = 'disabled';
                                }
                            }
                        }
                        else {*/
                        // var_dump("doesn't have selected option");
                        foreach ($config_opt->options as $opt) {
                            if (empty($opt->disabled) && empty($config_opt->selected)) {
                                $opt_val = $opt->value;

                                $match = array_filter($products_matches, function ($product) use ($conf_name, $opt_val) {
                                    return $product->get_attr_value($conf_name) == $opt_val;
                                });

                                // var_dump("matched for option $conf_name - $opt_val: " . count($match));

                                if (count($match)) {
                                    foreach ($match as $prod)
                                        $matches[$index][] = $prod;

                                    // var_dump("selected $conf_name: $opt_val");

                                    // $config_opt->selected = $opt_val;
                                    $config_opt->selected = $opt_val;
                                } else {
                                    $opt->disabled = $matched_products_num == 1 ? 'disabled' : '';
                                }
                            }
                        }

                        foreach ($config_opt->options as $opt) {
                            if (empty($opt->disabled) && !empty($config_opt->selected) && $matched_products_num == 1) {
                                if ($opt->value != $config_opt->selected)
                                    $opt->disabled = 'disabled';
                            }
                        }
                        // }

                        // var_dump(count(array_keys($matches[$index])));

                        $count_matches = array_count_values(array_column($matches[$index], 'productId'));
                        // var_dump($count_matches);

                        // echo '<hr />';
                    }

                    $index++;
                }
            }
        }

        if (count($click_matched_products) == 1) {
            $matched_product = current($click_matched_products);
            if ($matched_product !== false) {
                $this->defaultProduct = $matched_product;
            }
        } else {
            $count_matches = array_count_values(array_column($leader_matched_products, 'productId'));

            $matches_by_product = [];

            foreach ($leader_matched_products as $matched_product) {
                if (!isset($matches_by_product[$matched_product->productId]))
                    $matches_by_product[$matched_product->productId] = 0;

                foreach ($this->config_options as $config_opt) {
                    if ($matched_product->get_attr_value($config_opt->setting_name) == $config_opt->selected)
                        $matches_by_product[$matched_product->productId]++;
                }
            }

            $previous_attr_matches = 0;
            $matched_product_id = null;
            foreach ($matches_by_product as $prod_id => $attr_match) {
                if ($previous_attr_matches < $attr_match) {
                    $previous_attr_matches = $attr_match;
                    $matched_product_id = $prod_id;
                }
            }

            if ($matched_product_id !== null) {
                $matched_product = array_filter($leader_matched_products, function ($matched_prod) use ($matched_product_id) {
                    return $matched_prod->productId == $matched_product_id;
                });

                $matched_product = current($matched_product);

                if ($matched_product !== false) {
                    $this->defaultProduct = $matched_product;
                }
            }
        }

        foreach ($this->options as $option) {
            if (isset($request_data['option']) && is_array($request_data['option']) && isset($request_data['option'][$option->id]))
                $this->selected_options[] = (int) $request_data['option'][$option->id];
        }
    }

    public function get_name(): ?string
    {
        if (isset($this->name) && is_array($this->name) && count($this->name)) {
            $lang = PROPELLER_LANG;

            $found = array_filter($this->name, function ($obj) use ($lang) {
                return strtolower($obj->language) == strtolower($lang) && !empty($obj->value);
            });

            if (!count($found))
                $found = array_filter($this->name, function ($obj) {
                    return strtolower($obj->language) == strtolower(PROPELLER_FALLBACK_LANG) && !empty($obj->value);
                });

            if (count($found)) {
                $item = current($found);
                return $item !== false ? (string)$item->value : null;
            }

            return null;
        }

        return null;
    }

    public function get_price(): static
    {
        $data = $this->sanitize($_REQUEST);

        $price_incl_vat = $this->defaultProduct->price->net;
        $price_excl_vat = $this->defaultProduct->price->gross;

        // Process all options: both required and user-selected
        foreach ($this->options as $option) {
            $option->required = $option->isRequired == 'Y' ? true : false;

            if (isset($data['option']) && is_array($data['option']) && isset($data['option'][$option->id])) {
                // User has explicitly selected this option
                $selected_option = (int) $data['option'][$option->id];

                $found = array_filter($option->products, function ($obj) use ($selected_option) {
                    return $obj->productId == $selected_option;
                });

                if (count($found)) {
                    $selected_product = current($found);

                    $price_incl_vat += $selected_product->price->net;
                    $price_excl_vat += $selected_product->price->gross;
                }
            } else {
                if ($option->required) {
                    // Required option with no user selection - use default
                    $default_option_product = $option->defaultProduct->productId;

                    $found = array_filter($option->products, function ($obj) use ($default_option_product) {
                        return $obj->productId == $default_option_product;
                    });

                    if (count($found)) {
                        $selected_product = current($found);

                        $price_incl_vat += $selected_product->price->net;
                        $price_excl_vat += $selected_product->price->gross;
                    }
                }
            }
        }

        $this->defaultProduct->price->net = $price_incl_vat;
        $this->defaultProduct->price->gross = $price_excl_vat;

        return $this;
    }

    public function get_products(): array
    {
        return $this->products;
    }

    public function has_options(): bool
    {
        return isset($this->options) && !is_null($this->options) && is_array($this->options) && count($this->options) > 0;
    }

    public function get_options(): array
    {
        return $this->has_options() ? $this->options : [];
    }

    public function has_settings(): bool
    {
        return isset($this->config->settings) && !is_null($this->config->settings) && is_array($this->config->settings) && count($this->config->settings) > 0;
    }

    public function get_settings(): array
    {
        return $this->has_settings() ? $this->config->settings : [];
    }

    public function has_crossupsells(): bool
    {
        return isset($this->crossupsells) && is_array($this->crossupsells) && count($this->crossupsells) > 0;
    }

    public function has_slug(): bool
    {
        return isset($this->slug) && count($this->slug) && !empty($this->slug[0]->value);
    }

    public function get_slug(): ?string
    {
        if ($this->has_slug())
            return $this->slug[0]->value;

        return null;
    }

    public function get_config_options(): array
    {
        return $this->config_options;
    }

    public function has_track_attributes(): bool
    {
        return count($this->trackAttributes->items) > 0;
    }

    public function get_track_attr_by_name(string $name): ?Attribute
    {
        if (!is_array($this->trackAttributes->items))
            return null;

        $found = array_filter($this->trackAttributes->items, function ($obj) use ($name) {
            return $obj->attributeDescription->name == $name;
        });

        if (count($found))
            return current($found);

        return null;
    }

    public function get_track_attr_value(string $name): mixed
    {
        if (!is_array($this->trackAttributes->items))
            return null;

        $found = array_filter($this->trackAttributes->items, function ($obj) use ($name) {
            return $obj->attributeDescription->name == $name;
        });

        if (count($found)) {
            $item = current($found);
            return $item !== false ? $item->get_value() : null;
        }

        return null;
    }
}
