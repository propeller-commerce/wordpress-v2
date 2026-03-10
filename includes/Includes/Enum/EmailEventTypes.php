<?php
namespace Propeller\Includes\Enum;

if ( ! defined( 'ABSPATH' ) ) exit;

class EmailEventTypes {
    const ORDERCONFIRM = 'orderconfirm';
    const REGISTRATION = 'registration';
    const CAMPAIGN = 'campaign';
    const TRANSACTIONAL = 'transactional';
    const CUSTOM = 'custom';
    const SYSTEM = 'system';
    const ERROR = 'error';
}