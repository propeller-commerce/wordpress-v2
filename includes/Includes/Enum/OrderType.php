<?php
namespace Propeller\Includes\Enum;

if ( ! defined( 'ABSPATH' ) ) exit;

class OrderType {
    const REGULAR = 0;
    const DROPSHIPMENT = 1;
    const PICKUP = 2;
    // const QUICK = 3;    
    // const PURCHASE = 4;    
}