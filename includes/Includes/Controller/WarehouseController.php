<?php

namespace Propeller\Includes\Controller;

if ( ! defined( 'ABSPATH' ) ) exit;

class WarehouseController extends BaseController {
    protected $type = 'warehouse';
    protected $model;
    
    public function __construct() {
        parent::__construct();

        $this->model = $this->load_model('warehouse');
    }

    public function get_warehouses($args = []) {
        $type = 'warehouses';

        $params = count($args) ? $args : [];

        $gql = $this->model->get_warehouses($params);

        return $this->query($gql, $type);
    }
}