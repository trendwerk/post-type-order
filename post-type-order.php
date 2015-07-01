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
 * Set plugin path
 */
define('TW_PTO_PLUGIN_PATH', plugins_url('', __FILE__));

/**
 * Initialize plugin
 */
include_once(__DIR__ . '/assets/includes/PostTypeOrder.php');

new PostTypeOrder;
