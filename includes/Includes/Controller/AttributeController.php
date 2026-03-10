<?php 

namespace Propeller\Includes\Controller;

if ( ! defined( 'ABSPATH' ) ) exit;

class AttributeController extends BaseController {
    protected $model;

    public function __construct() {
        parent::__construct();

        $this->model = $this->load_model('attribute');
    }

    public function create_description($attrdescription_args) {
        $type = 'attributeDescriptionCreate';
        
        $gql = $this->model->create_description($attrdescription_args);

        $response = $this->query($gql, $type);

        return $response;
    }

    public function get_description($attrdescription_args) {
        $type = 'attributeDescriptions';
        
        $gql = $this->model->get_description($attrdescription_args);

        $response = $this->query($gql, $type);

        return $response;
    }

    public function create_attribute($attr_args) {
        $type = 'attributeCreate';
        
        $gql = $this->model->create_attribute($attr_args);

        $response = $this->query($gql, $type);

        return $response;
    }

    public function update_attribute($attr_id, $attr_args) {
        $type = 'attributeUpdate';
        
        $gql = $this->model->update_attribute($attr_id, $attr_args);

        $response = $this->query($gql, $type);

        return $response;
    }

    public function get_attr_by_name($name, $attributes) {
        if (!is_array($attributes))
            return null;
        
        $found = array_filter($attributes, function($obj) use ($name) { 
            return $obj->attributeDescription->name == $name; 
        });

        if (count($found))
            return current($found);

        return null;
    }
}