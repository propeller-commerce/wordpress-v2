<?php
namespace Propeller\Includes\Controller;

if ( ! defined( 'ABSPATH' ) ) exit;

use stdClass;

class FilterController extends BaseController {

    public $type = 'filter';
    protected $slug = '';
    protected $action = '';
    protected $prop = '';
    protected $liststyle = 'blocks';
    protected $obid = '';
    protected $attr_filters;
    public $filters = [];
    
    
    public function __construct($attrs, $prices = []) {
        parent::__construct();

        $this->attr_filters = [];
        
        $tmp_filters = [];

        if (is_array($prices) && sizeof($prices)) {
            $price_filter = new stdClass();

            $price_filter->type = 'price';
            $price_filter->subtype = 'price';
            $price_filter->min = $prices[0];
            $price_filter->max = $prices[1];

            $tmp_filters[] = $price_filter;
        }

        if ($attrs)
            $this->attr_filters = array_merge($tmp_filters, $attrs->get_non_empty_attrs());
        
        $this->categorize();
    }

    public function getAttributeFilters() {
        return $this->attr_filters;
    }

    public function getFilters() {
        return $this->filters;
    }

    public function sort_alpha() {
        $price_filter = null;

        $other_filters = [];

        foreach ($this->filters as $filter) {
            if ($filter->type == 'price') {
                $price_filter = $filter;
                continue;
            }                

            $other_filters[] = $filter;
        }


        // var_dump($this->filters);
        usort($other_filters, function($a, $b) {
            return strcmp($a->description, $b->description);
        });

        unset($this->filters);

        $this->filters = [];

        if ($price_filter)
            $this->filters[0] = $price_filter;

        foreach ($other_filters as $filter)
            $this->filters[] = $filter;
    }

    // must follow rules as "[num]. description" (ex. "4. Size")
    public function sort_numeric() {
        $price_filter = null;

        $other_filters = [];

        foreach ($this->filters as $filter) {
            if ($filter->type == 'price') {
                $price_filter = $filter;
                continue;
            }

            preg_match('/(\d+)\.\s(.*)/', $filter->description, $description_arr);

            if ($description_arr && count($description_arr)) {
                $filter->index = (int) $description_arr[1];
                $filter->description = $description_arr[2];
            }

            $other_filters[] = $filter;
        }


        // var_dump($this->filters);
        usort($other_filters, function($a, $b) {
            if (isset($a->index) && isset($b->index))
                return $a->index > $b->index;
        });

        unset($this->filters);

        $this->filters = [];

        if ($price_filter)
            $this->filters[0] = $price_filter;

        foreach ($other_filters as $filter)
            $this->filters[] = $filter;
    }

    public function draw($initial_expanded = true) {
        if (!count($this->filters))
            $this->filters = $this->attr_filters;

        foreach ($this->filters as $filter) {
            if (!isset($filter->type))
                continue;

            $expanded = $initial_expanded;
            
            if (isset($_REQUEST['active_filter']) && !empty($_REQUEST['active_filter']) && isset($filter->searchId) && $_REQUEST['active_filter'] == $filter->searchId && !$expanded)
                $expanded = true;

            require $this->load_template('partials', '/category/filter' . DIRECTORY_SEPARATOR . 'propeller-filter-' . $filter->subtype . '.php');
        }
    }

    private function categorize() {
        foreach ($this->attr_filters as $attr) {
            $this->filters[] = $attr;

            // if (!isset($this->filters[$attr->type]))
            //     $this->filters[$attr->type] = [];
            
            // $this->filters[$attr->type][] = $attr;
        }
    }

    public function set_slug($slug) {
        $this->slug = $slug;
    }

    public function set_action($action) {
        $this->action = $action;
    }

    public function set_prop($name) {
        $this->prop = $name;
    }

    public function set_liststyle($style) {
        $this->liststyle = $style;
    }

    public function set_obid($obid) {
        $this->obid = $obid;
    }

    public function get_slug() {
        return $this->slug;
    }

    public function get_action() {
        return $this->action;
    }

    public function get_prop() {
        return $this->prop;
    }

    public function get_liststyle() {
        return $this->liststyle;
    }

    public function get_obid() {
        return $this->obid;
    }
}