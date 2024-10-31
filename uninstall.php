<?php
/**
 * If uninstall.php is not called by WordPress, die.
 *
 * @package MultilevelNavigationMenu
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

global $wpdb;
// Remove options from option table.
delete_option( 'mnmwp-switch' );