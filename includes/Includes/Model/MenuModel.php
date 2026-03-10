<?php

namespace Propeller\Includes\Model;

if ( ! defined( 'ABSPATH' ) ) exit;

use stdClass;

class MenuModel extends BaseModel {
    public function __construct() {
        parent::__construct();
    }

    public function get_menu($base_catalog_id, $language, $depth = 3) {
        $category_gql = "
            categories (hidden: N) {
                ... WPCategoryMinimalFragment
        ";

        $gql = "
            query WPMenu (
                \$categoryId: Float,
                \$language: String
            ){
                category(categoryId: \$categoryId) {
        ";

        for ($i = 0; $i < $depth; $i++) {
            $gql .= $category_gql;
        }

        for ($i = 0; $i < $depth; $i++) {
            $gql .= " } ";
        }

        $gql .= "}
            }
        ";

        $variables = [
            'categoryId' => $base_catalog_id,
            'language' => $language
        ];

        $return = new stdClass();
        $return->query = implode("\n\n", [
            CategoryModel::category_minimal_fragment()->query, 
            $gql
        ]);
        $return->variables = $variables;

        return $return;
    }
}