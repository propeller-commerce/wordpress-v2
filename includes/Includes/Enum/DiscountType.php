<?php
namespace Propeller\Includes\Enum;

if ( ! defined( 'ABSPATH' ) ) exit;

class DiscountType {
    const NONE = 'N';
    const PERCENTAGE = 'P';
    const AMOUNT = 'A';
}