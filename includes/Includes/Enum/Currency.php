<?php

namespace Propeller\Includes\Enum;

if ( ! defined( 'ABSPATH' ) ) exit;

class Currency
{
    const CURRENCY_EUR = '&euro;';
    const CURRENCY_USD = '$';
    const CURRENCY_GBP = '&pound;';
    const CURRENCY_YPY = '&yen;';
    const CURRENCY_RUB = '&#8381;';

    public static $currencies = [
        "EUR" => "&euro;",
        "USD" => "$",
        "GBP" => "&pound;",
        "JPY" => "&yen;",
        "RUB" => "&#8381;",
    ];
}
