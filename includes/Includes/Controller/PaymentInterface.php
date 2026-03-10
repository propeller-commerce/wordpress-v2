<?php
namespace Propeller\Includes\Controller;

if ( ! defined( 'ABSPATH' ) ) exit;

interface PaymentInterface {
    public function create($args);

    public function get($args);
}