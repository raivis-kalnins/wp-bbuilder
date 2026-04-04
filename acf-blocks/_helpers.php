<?php
if (!defined('ABSPATH')) exit;

function wpbb_acf_field($key, $default = '') {
    return function_exists('get_field') ? (get_field($key) ?: $default) : $default;
}
