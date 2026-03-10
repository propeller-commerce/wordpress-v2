<?php

namespace Propeller\Includes\Controller;

if ( ! defined( 'ABSPATH' ) ) exit;

use Propeller\Includes\Enum\MediaImagesType;
use Propeller\Includes\Enum\MediaType;
use Propeller\Includes\Enum\PageType;
use Propeller\Includes\Object\FilterArray;
use Propeller\Includes\Object\Machine;
use Propeller\Includes\Object\SparePart;
use Propeller\Includes\Query\Media;
use Propeller\PropellerUtils;
use Propeller\PropellerHelper;
use stdClass;

class MachineController extends BaseController
{
    protected $model;
    public $data;
    public $machines;
    public $attributes;
    public $filters;
    public $parts;
    public $term;

    public $offset_arr = [12, 24, 48];
    public $sort_arr = [];
    public $sort_order = [];

    protected $media_type = "SparePartsMachineMedia";
    protected $media_search_type = "ObjectMachineMediaSearchInput";

    const SOURCE = 'OTTEVANGER';

    public function __construct()
    {
        parent::__construct();

        $this->model = $this->load_model('machine');
    }

    public function machine_title($data)
    {
        require $this->load_template('partials', '/category/propeller-machine-listing-title.php');
    }

    public function machine_description($data)
    {
        require $this->load_template('partials', '/category/propeller-machine-listing-description.php');
    }

    public function machine_listing_grid($obj, $machines, $parts, $paging_data, $sort, $prop_name, $prop_value, $do_action)
    {
        $display_class = isset($_REQUEST['view']) && !empty($_REQUEST['view']) ? sanitize_text_field($_REQUEST['view']) : 'blocks';

        require $this->load_template('partials', '/product/propeller-machine-grid.php');
    }

    public function machine_card($machine, $obj)
    {
        require $this->load_template('partials', '/product/propeller-machine-card.php');
    }

    public function machine_listing_pre_grid($data, $obj, $sort, $prop_name, $prop_value, $do_action)
    {
        $selected_filters = $obj->get_selected_filters($obj->filters->getFilters());

        $data = $this->sanitize($_REQUEST);
        if (
            isset($data['price_from']) && $data['price_from'] != '' && is_numeric($data['price_from']) &&
            isset($data['price_to']) && $data['price_to'] != '' && is_numeric($data['price_to'])
        ) {

            $price_filter = new stdClass();
            $price_filter->filter = new stdClass();
            $price_filter->filter->type = 'price';
            $price_filter->filter->searchId = 'price';

            $price_filter->value = PropellerHelper::currency() . PropellerHelper::formatPrice((int) $data['price_from']) . ' - ' . PropellerHelper::currency() . PropellerHelper::formatPrice((int) $data['price_to']);
            $price_filter->price_from = (int) $data['price_from'];
            $price_filter->price_to = (int) $data['price_to'];

            array_unshift($selected_filters, $price_filter);
        }
        require $this->load_template('partials', '/other/propeller-machine-pre-grid.php');
    }

    // public function machine_gecommerce_listing($products, $obj) {
    //     require $this->load_template('partials', '/category/propeller-gecommerce-listing.php');
    // }

    public function machine_listing_machines($machines, $parts, $obj)
    {
        require $this->load_template('partials', '/category/propeller-machine-listing-machines.php');
    }

    public function machine_listing_pagination($paging_data, $prop_name, $prop_value, $do_action)
    {
        $prev = $paging_data->page - 1;
        $prev_disabled = false;

        if ($prev < 1) {
            $prev = 1;
            $prev_disabled = 'disabled';
        }

        $next = $paging_data->page + 1;
        $next_disabled = false;

        if ($next >= $paging_data->pages) {
            $next = $paging_data->pages;

            if ($paging_data->page == $next)
                $next_disabled = 'disabled';
        }

        require $this->load_template('partials', '/other/propeller-pagination.php');
    }

    public function machine_menu($data)
    {
        $back_url = null;

        $slug = get_query_var('slug');

        if (is_array($slug) && count($slug) > 1) {
            unset($slug[count($slug) - 1]);

            $back_url = $this->buildUrl(PageController::get_slug(PageType::MACHINES_PAGE), implode('/', $slug));
        }

        require $this->load_template('partials', '/category/propeller-machine-listing-categories.php');
    }

    public function buildMachineUrl($uri, $slug)
    {
        return $uri . $slug . '/';
    }

    public function machine_listing($applied_filters = [], $is_ajax = false)
    {
        global $propel;

        if (!$applied_filters || !sizeof($applied_filters))
            $applied_filters = PropellerUtils::sanitize($_REQUEST);

        $filters_applied = $this->process_filters($applied_filters);
        $qry_params = $this->build_search_arguments($applied_filters);

        $qry_params = array_merge($qry_params, $filters_applied);

        // var_dump($applied_filters);
        // var_dump($filters_applied);
        // var_dump($qry_params);
        $sort = isset($applied_filters['sortInputs']) && !empty($applied_filters['sortInputs'])
            ? explode(',', $applied_filters['sortInputs'])
            : PROPELLER_DEFAULT_SORT_FIELD . ',' . PROPELLER_DEFAULT_SORT_DIRECTION;

        //$sort = isset($applied_filters['sortInputs']) && !empty($applied_filters['sortInputs']) ? $applied_filters['sortInputs'] : array_key_first($this->sort_arr) . ',' . array_key_first($this->sort_order);

        $slug = isset($applied_filters['slug']) ? $applied_filters['slug'] : get_query_var('slug');

        $style = isset($applied_filters['view']) ? $applied_filters['view'] : 'blocks';

        $term = isset($applied_filters['term']) ? $applied_filters['term'] : get_query_var('term');
        $term = wp_unslash($term);

        if (is_array($slug))
            $slug = $slug[count($slug) - 1];

        $this->data = isset($propel['data'])
            ? $propel['data']
            : (empty($slug) ? $this->get_installations($qry_params, $is_ajax) : $this->get_machines($slug, $qry_params, $is_ajax));

        $this->machines = [];

        if (isset($this->data->items)) {
            foreach ($this->data->items as $machine) 
                $this->machines[] = new Machine($machine);
        } else if (isset($this->data->machines)) {
            foreach ($this->data->machines as $machine) 
                $this->machines[] = new Machine($machine);
        }

        $this->parts = new stdClass();
        $this->parts->itemsFound = 0;
        $part_items = [];

        if (isset($this->data->sparePartProducts) && isset($this->data->sparePartProducts->items)) {
            $this->parts = $this->data->sparePartProducts;

            foreach ($this->data->sparePartProducts->items as $part) {
                if (!is_object($part->product) || !count($part->product->slug)) {
                    continue;
                }

                $part_items[] = new SparePart($part);
            }
        }

        $this->parts->items = $part_items;

        $this->attributes = [];
        if (isset($this->data->sparePartProducts->filters))
            $this->attributes = new FilterArray($this->data->sparePartProducts->filters);

        $this->filters = new FilterController($this->attributes, [$this->data->sparePartProducts->minPrice, $this->data->sparePartProducts->maxPrice]);
        $this->filters->set_slug($slug);
        $this->filters->set_action('do_machine');
        $this->filters->set_prop('slug');
        $this->filters->set_liststyle($style);

        $this->pagename = PageController::get_slug(PageType::MACHINES_PAGE);

        $paging_data = $this->data;
        $do_action = "do_machine";
        $prop_name = "slug";
        $prop_value = $slug;
        $obid = "";

        $this->term = is_array($term) ? $term[0] : $term;

        ob_start();

        if ($is_ajax) {
            $response = new stdClass();

            apply_filters('propel_machine_grid', $this, $this->machines, $this->parts, $paging_data, $sort, $prop_name, $prop_value, $do_action);
            $response->content = ob_get_clean();

            ob_start();
            apply_filters('propel_category_filters', $this->filters);
            $filters_content = ob_get_clean();

            $response->filters = $filters_content;

            $response->filters_arr = $this->filters->filters;

            return $response;
        } else {
            require $this->load_template('templates', '/propeller-machine-listing.php');
        }

        return ob_get_clean();
    }

    public function get_installations($qry_params, $is_ajax = false)
    {
        $installations = new stdClass();
        $installations->items = [];

        if (SessionController::has('MY_INSTALLATIONS') && !empty(SessionController::get('MY_INSTALLATIONS'))) {
            if ($is_ajax)
                $qry_params = $this->build_search_arguments($qry_params);

            $user_installations = explode(',', SessionController::get('MY_INSTALLATIONS'));

            $machine_queries = [];
            $index = 1;

            $spareparts_images_fragment = Media::get([
                'name' => MediaImagesType::MEDIUM,
                'offset' => 1
            ], MediaType::IMAGES, true);

            $spareparts_documents_fragment = Media::get([], MediaType::DOCUMENTS, true);

            $machines_params = [
                'language' => "EN",
                'parts_img_search' => $spareparts_images_fragment->variables['parts_img_search'],
                'parts_doc_search' => $spareparts_documents_fragment->variables['parts_doc_search'],
                'source' => self::SOURCE
            ];

            foreach ($user_installations as $installation_id) {
                $machineId = trim($installation_id);

                $machine_queries[] = $this->model->get_machine_query($index);

                $machines_params['sourceId_' . $index] = $machineId;

                $index++;
            }

            $params = array_merge(
                $machines_params,
                $spareparts_documents_fragment->variables,
                $spareparts_images_fragment->variables
            );

            $gql = $this->model->installations(
                $machine_queries,
                $spareparts_images_fragment,
                $spareparts_documents_fragment,
                $params
            );

            // var_dump($gql->query);
            // var_dump(json_encode($gql->variables));

            $result = $this->query($gql, null);

            if ($index >= 1) {
                for ($i = 1; $i <= $index; $i++) {
                    $prop = "machine_$i";

                    if (is_object($result->$prop))
                        $installations->items[] = $result->$prop;
                }
            }
        }

        return $installations;
    }

    public function get_machines($slug, $qry_params = null, $is_ajax = false)
    {
        $type = 'machine';

        if (!isset($qry_params['language']))
            $qry_params['language'] = PROPELLER_LANG;

        if (!isset($qry_params['hidden']))
            $qry_params['hidden'] = false;

        if (!isset($qry_params['offset']))
            $qry_params['offset'] = intval(PROPELLER_DEFAULT_OFFSET);

        if (!isset($qry_params['sortInputs']))
            $qry_params['sortInputs'] = ['field' => PROPELLER_DEFAULT_SORT_FIELD, 'order' => PROPELLER_DEFAULT_SORT_DIRECTION];

        if (!isset($qry_params['statuses']))
            $qry_params['statuses'] = ["A", "P", "T", "S"];

        $machines_lang = "EN"; // Hardcoded as suggested by Wouter & Mark

        $gql = $this->model->get_machines(
            [
                'slug' => $slug
            ],
            $qry_params,
            Media::get([
                'name' => MediaImagesType::MEDIUM,
                'offset' => 1
            ], MediaType::IMAGES),
            Media::get([], MediaType::DOCUMENTS),
            Media::get([
                'name' => MediaImagesType::MEDIUM,
                'offset' => 1
            ], MediaType::IMAGES, true),
            Media::get([], MediaType::DOCUMENTS, true),
            PROPELLER_LANG
        );

        // var_dump($gql->query);
        // var_dump(json_encode($gql->variables));

        return $this->query($gql, $type);
    }
}
