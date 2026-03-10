<?php
namespace Propeller\Includes\Enum;

if ( ! defined( 'ABSPATH' ) ) exit;

class OrderItemClass {
    const PRODUCT = 'product';
    const CLUSTER = 'cluster';
    const SURCHARGE = 'surcharge';
    const INCENTIVE = 'incentive';
}