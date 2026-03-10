<?php
namespace Propeller\Includes\Enum;

if ( ! defined( 'ABSPATH' ) ) exit;

class OrderStatus {
    const ORDER_STATUS_NEW       = 'NEW';
    const ORDER_STATUS_REQUEST   = 'REQUEST';
    const ORDER_STATUS_QUOTATION   = 'QUOTATION';
    const ORDER_STATUS_VALIDATED = 'VALIDATED';
    const ORDER_STATUS_CONFIRMED = 'CONFIRMED';
    const ORDER_STATUS_ARCHIVED  = 'ARCHIVED';
    const ORDER_STATUS_UNFINISHED  = 'UNFINISHED';
}