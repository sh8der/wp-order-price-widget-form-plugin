<?php
/*
*Plugin Name: Order Price
*Plugin URI: https://inversion.kz
*Description: Виджет обратной формы всплывающий на всех страницах, на подобии redConnect, для сбора почтовых ящиков. 
*Author: Кирилл Шрейдер
*Author URI: https://sh8der.ru
*Text Domain: order-price
*Domain Path: /languages/
*Version: 1.0.0
*License: GPL2
*License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

define( 'INV_PLUGIN_ACTIVE', true );

define( 'INV_PLUGIN_DIR', dirname( __FILE__ ) );
define( 'INV_PLUGIN_BASE', plugin_basename( __FILE__ ) );
define( 'INV_PLUGIN_PATH', WP_PLUGIN_URL . '/order-price' );

require_once INV_PLUGIN_DIR . '/includes/functions.php';
require_once INV_PLUGIN_DIR . '/includes/run.php';

add_action( 'init', 'INV_register_table_name', 1 );
add_action( 'switch_blog', 'INV_register_table_name' );

function INV_register_table_name() {
	global $wpdb;
    $wpdb->order_price_table = "{$wpdb->prefix}inv_order_price_form";
}

register_activation_hook( __FILE__, 'INV_plugin_activate');
register_deactivation_hook( __FILE__, 'INV_plugin_deactivate');
register_uninstall_hook( __FILE__, 'INV_plugin_uninstall');

function INV_plugin_activate() {
	require_once INV_PLUGIN_DIR . '/setup.php';
}

function INV_plugin_deactivate() {
// nothing
}

function INV_plugin_uninstall() {
// nothing
}
