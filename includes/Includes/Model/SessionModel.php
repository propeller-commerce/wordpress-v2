<?php

namespace Propeller\Includes\Model;

if ( ! defined( 'ABSPATH' ) ) exit;

use stdClass;

class SessionModel {
    public function __construct() {
        
    }

    public function start_session($site_id) {
        $gql = '
            mutation WPSessionStartMutation(
                $site_id: Int
            ){
                startSession(siteId: $site_id) {
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
            'site_id' => $site_id
        ];

        return $return;
    }
}