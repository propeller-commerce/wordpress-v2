<?php

namespace Propeller\Includes\Model;

if ( ! defined( 'ABSPATH' ) ) exit;

use stdClass;

class AuthModel extends BaseModel {
    public function __construct() {
        parent::__construct();
    }

    public function login($username, $password, $provider = "") {
        $gql = '
            mutation WPLoginMutation(
                $login_input: LoginInput!
            ){
                login(
                    input: $login_input
                ) {
                    session {
                        ... WPSessionFragment
                    }
                }
            }
        ';

        $queries = [
            UserModel::session_fragment()->query,
            $gql
        ];

        $return = new stdClass();
        $return->query = implode("\n\n", $queries);
        $return->variables = [
            'login_input' => [
                'email' => $username,
                'password' => $password,
                'provider' => $provider
            ]
        ];

        return $return;
    }

    public function magic_token_login($id) {
        $gql = '
            mutation WPMagicTokenLoginMutation(
                $magic_token: String!
            ){
                magicTokenLogin(
                    id: $magic_token
                ) {
                    session {
                        ... WPSessionFragment
                    }
                }
            }
        ';

        $queries = [
            UserModel::session_fragment()->query,
            $gql
        ];

        $return = new stdClass();
        $return->query = implode("\n\n", $queries);
        $return->variables = [
            'magic_token' => $id
        ];

        return $return;
    }

    public function refresh($token_params) {
        $gql = '
            mutation WPRefreshTokenMutation(
                $token_input: ExchangeRefreshTokenInput!
            ){
                exchangeRefreshToken(input: $token_input) {
                    access_token
                    refresh_token
                    expires_in
                    token_type
                    user_id
                }
            }
        ';

        $return = new stdClass();
        $return->query = $gql;
        $return->variables = [
            'token_input' => $token_params
        ];

        return $return;
    }

    public function logout($args) {
        $gql = '
            mutation WPLogoutMutation(
                $siteId: Int
            ){
                logout(siteId: $siteId) {
                    todo
                }
            }       
        ';

        $return = new stdClass();
        $return->query = $gql;
        $return->variables = [
            'siteId' => $args['siteId']
        ];

        return $return;
    }

    public function reset_claims($email, $uid) {
        $gql = "
            mutation WPResetClaims(
                \$email: String!
                \$uid: String!
            ) {
                claimsReset(email: \$email, uid: \$uid)
            }       
        ";

        $return = new stdClass();
        $return->query = $gql;
        $return->variables = [
            'email' => $email,
            'uid' => $uid
        ];

        return $return;
    }

    public function create($email, $password, $displayName = '') {
        $gql = "
            mutation WPAuthenticationCreate (
                \$input: CreateAuthenticationInput!
            ){
                authenticationCreate(input: \$input) {
                    session {
                        email
                    }
                }
            }        
        ";

        $return = new stdClass();
        $return->query = $gql;
        $return->variables = [
            'input' => [
                'email' => $email,
                'password' => $password,
                'displayName' => $displayName
            ]
        ];

        return $return;
    }
}

