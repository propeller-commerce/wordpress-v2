<?php
namespace Propeller\Includes\Enum;

if ( ! defined( 'ABSPATH' ) ) exit;

class AddressType {
    const DELIVERY = 'delivery';
    const INVOICE = 'invoice';
    const HOME = 'home';
}