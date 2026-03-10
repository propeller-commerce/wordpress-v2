<?php 
namespace Propeller\Includes\Controller;

if ( ! defined( 'ABSPATH' ) ) exit;

use Propeller\Includes\Controller\CategoryController;

class CategoryAjaxController extends BaseAjaxController {
    protected $category;

    public function __construct() { 
        parent::__construct();

        $this->category = new CategoryController();
    }

    public function do_filter() {
        $this->init_ajax();
        
	    $data = $this->sanitize($_POST);

        unset($data['action']);

        $response = $this->category->product_listing($data, true);

        die(json_encode($response));
    }
}