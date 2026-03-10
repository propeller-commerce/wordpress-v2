<?php

namespace Propeller\Includes\Controller;

if ( ! defined( 'ABSPATH' ) ) exit;

class EmailController extends BaseController {
    protected $model;

    public function __construct() {
        parent::__construct();

        $this->model = $this->load_model('email'); 
    }

    public function send_propeller_email($params, $args = [], $attachments = [], $vars = []) {
        $type = 'publishEmailEvent';

        if (isset($args['orderId'])) $params['orderId'] = $args['orderId'];
        if (isset($args['userId'])) $params['userId'] = $args['userId'];
        if (isset($args['letterId'])) $params['letterId'] = $args['letterId'];
        if (isset($args['language'])) $params['language'] = $args['language'];
        
        if (is_array($attachments) && count($attachments))
            $params['attachments'] = $attachments;

        $params['variables'] = $vars;

        $gql = $this->model->send_propeller_email($params);

        return $this->query($gql, $type);
    }

    public static function send_wp_email($to, $from, $subject, $content, $cc = '', $bcc = '', $attachments = []) {
        $headers = [];

        $headers[] = "From: $from";

        if (!empty($cc)) $headers[] = "Cc: $cc";
        if (!empty($bcc)) $headers[] = "Bcc: $bcc";
        
        return wp_mail($to, $subject, $content, $headers, $attachments);
    }
}