<?php
namespace Propeller\Includes\Enum;

if ( ! defined( 'ABSPATH' ) ) exit;

use ReflectionClass;

class SsoProviders {
    const FIREBASE = 'Firebase';

    static function getConstants() {
        $oClass = new ReflectionClass(__CLASS__);

        return $oClass->getConstants();
    }
}