<?php

namespace Propeller\Includes\Controller;

if ( ! defined( 'ABSPATH' ) ) exit;

class AuthController extends BaseController {
    protected $model;

    public function __construct() {
        parent::__construct();

        $this->model = $this->load_model('auth');
    }

    public function login($email, $password, $provider = '') {
        $type = 'login';

        $gql = $this->model->login($email, $password, $provider);
        
        $loginData = $this->query($gql, $type);

        return $loginData;
    }

    public function magic_token_login($magic_token) {
        $type = 'magicTokenLogin';

        $gql = $this->model->magic_token_login($magic_token);
        
        $loginData = $this->query($gql, $type);

        return $loginData;
    }

    public function refresh() {
        $type = 'exchangeRefreshToken';

        $params = [
            'refreshToken' => SessionController::get(PROPELLER_REFRESH_TOKEN)
        ];

        $gql = $this->model->refresh($params);

        $loginData = $this->query($gql, $type);

        return $loginData;
    }

    public function logout() {
        $type = 'logout';

        $gql = $this->model->logout(['siteId' => PROPELLER_SITE_ID]);
        
        $logoutData = $this->query($gql, $type);

        return $logoutData;
    }

    public function create($args) {
        $type = 'authenticationCreate';

        $gql = $this->model->create($args['email'], $args['password'], $args['displayName']);

        return $this->query($gql, $type);
    }

    public function reset_claims($email, $uid) {
        $type = 'claimsReset';

        $gql = $this->model->reset_claims($email, $uid);

        return $this->query($gql, $type);
    }
}