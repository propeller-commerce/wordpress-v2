<?php
namespace Propeller\Includes\Enum;

if ( ! defined( 'ABSPATH' ) ) exit;

class TransactionStatuses {
    const FAILED = 'FAILED';
    const OPEN = 'OPEN';
    const PENDING = 'PENDING';
    const SUCCESS = 'SUCCESS';
}