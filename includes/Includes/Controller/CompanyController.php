<?php

namespace Propeller\Includes\Controller;

if ( ! defined( 'ABSPATH' ) ) exit;

class CompanyController extends BaseController
{
    protected $model;

    public function __construct()
    {
        parent::__construct();

        $this->model = $this->load_model('company');
    }

    public function create($args)
    {
        $type = 'companyCreate';

        $params = [
            'name' => $args['name'],
            'taxNumber' => $args['taxNumber'],
            'cocNumber' => $args['cocNumber']
        ];

        $gql = $this->model->create($params);

        return $this->query($gql, $type);
    }

    public function get($id)
    {
        $type = 'company';

        $gql = $this->model->get(['company_id' => $id]);

        return $this->query($gql, $type);
    }
}
