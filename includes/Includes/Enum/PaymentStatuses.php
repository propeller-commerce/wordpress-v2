<?php
namespace Propeller\Includes\Enum;

if ( ! defined( 'ABSPATH' ) ) exit;

class PaymentStatuses {
    const AUTHORIZED = 'AUTHORIZED';
    const CANCELLED = 'CANCELLED';
    const CHARGEBACK = 'CHARGEBACK';
    const EXPIRED = 'EXPIRED';
    const FAILED = 'FAILED';
    const OPEN = 'OPEN';
    const PAID = 'PAID';
    const PENDING = 'PENDING';
    const REFUNDED = 'REFUNDED';
    const UNKNOWN = 'UNKNOWN';
    const EMPTY = '';
}