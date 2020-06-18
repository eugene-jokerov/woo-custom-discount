<?php

/**
 * Plugin Name: Woo Custom Discount
 * Plugin URI: https://woocommerce.com/
 * Description: Кастомные скидки для WooCommerce
 * Version: 1.0.0
 * Author: Eugene Jokerov
 */

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'WCD_PLUGIN_PATH' ) ) {
    define( 'WCD_PLUGIN_PATH', __DIR__ );
}

if ( ! defined( 'WCD_PLUGIN_URL' ) ) {
    define( 'WCD_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}

include_once WCD_PLUGIN_PATH. '/includes/autoload.php';
JWP\WCD\Plugin::instance();
