<?php
/**
 * Plugin Name: Post type order
 * Description: Order posts in any post type.
 *
 * Plugin URI: https://github.com/trendwerk/post-type-order
 *
 * Author: Trendwerk
 * Author URI: https://github.com/trendwerk
 *
 * Version: 2.0.0
 */
namespace Trendwerk\PostTypeOrder;

/**
 * Define plugin url
 */
if (! defined('TW_PTO_PLUGIN_PATH')) {
    define('TW_PTO_PLUGIN_PATH', plugins_url('', __FILE__));
}

/**
 * Include autoloader
 */
include_once('assets/includes/autoload.php');

/**
 * Instantiate classes
 */
new PostTypeOrder;
