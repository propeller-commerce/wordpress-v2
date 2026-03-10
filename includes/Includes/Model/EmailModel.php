<?php

namespace Propeller\Includes\Model;

if ( ! defined( 'ABSPATH' ) ) exit;

use stdClass;

class EmailModel extends BaseModel {
    public function __construct() {
        
    }

    public function send_propeller_email($email_args) {
        $gql = '
            mutation WPPublishEmailEventMutation(
                $email_input: EmailEventInput!
            ){
                publishEmailEvent(input: $email_input) {
                    success
                    messageId
                }
            }
        ';

        $return = new stdClass();
        $return->query = $gql;
        $return->variables = [
            'email_input' => $email_args
        ];

        return $return;
    }
}
