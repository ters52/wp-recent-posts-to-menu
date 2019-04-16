<?php

namespace PIM;

/**
 * Plugin Name: Recent Posts in Menu
 * Plugin URI: https://github.com/ters52/wp-recent-posts-to-menu
 * Description: Add list of recent posts to WP Menu
 * Version: 1.0
 * Author: ters52
 * Author URI: https://github.com/ters52
 * License: GPL2
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access
if (!defined('ABSPATH')) exit;

// Plugin version
define('PIM_VER', '1.0');

// Plugin path 
define('PIM_PATH', __FILE__);


if (is_admin()) {
    // Instantiate Admin class
    require_once('inc/class-pim-admin.php');
    new PIM_Admin();
} else {
    // Instantiate Frontend class
    require_once('inc/class-pim-client.php');
    new PIM_Client();
}




