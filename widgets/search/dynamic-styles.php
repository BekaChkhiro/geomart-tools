<?php

if (!defined('ABSPATH')) {
    exit;
}

function geomart_search_get_dynamic_styles() {
    $styles = "
        .geomart-search-input {
            border-color: #eaeaea;
            border-radius: 8px;
            background-color: #ffffff;
        }

        .geomart-search-input:focus {
            border-color: #ea3e3c;
            box-shadow: none;
        }

        .geomart-search-results {
            background: #ffffff;
            border-radius: 8px;
        }

        .geomart-search-result-price {
            color: #4054b2;
        }
    ";

    return $styles;
}

function geomart_hex2rgba($color, $opacity = false) {
    $default = 'rgb(0,0,0)';
 
    if (empty($color)) {
        return $default;
    }

    if ($color[0] == '#') {
        $color = substr($color, 1);
    }
 
    if (strlen($color) == 6) {
        $hex = array($color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5]);
    } elseif (strlen($color) == 3) {
        $hex = array($color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2]);
    } else {
        return $default;
    }
 
    $rgb = array_map('hexdec', $hex);
 
    if ($opacity) {
        return 'rgba(' . implode(',', $rgb) . ',' . $opacity . ')';
    }
 
    return 'rgb(' . implode(',', $rgb) . ')';
}
