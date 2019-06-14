<?php

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

define( 'INV_PLUGIN_DIR', dirname( __FILE__ ) );
require_once INV_PLUGIN_DIR . '/includes/functions.php';

/*Удаляем таблицу*/
INV_drop_table();
