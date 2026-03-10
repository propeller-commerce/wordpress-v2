<?php
namespace Propeller\Includes\Enum;

if ( ! defined( 'ABSPATH' ) ) exit;

class ImageResizeFilter {
    const NEAREST = 'NEAREST';
    const BILINEAR = 'BILINEAR';
    const BICUBIC = 'BICUBIC';
    const LANCZOS2 = 'LANCZOS2';
    const LANCZOS3 = 'LANCZOS3';
}