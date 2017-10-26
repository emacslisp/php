<?php
define( 'WPINC', 'wp-includes' );

require( ABSPATH . WPINC . '/load.php' );
require( ABSPATH . WPINC . '/default-constants.php' );
require_once( ABSPATH . WPINC . '/l10n.php' );
require_once( ABSPATH . WPINC . '/class-wp-locale.php' );
//require_once( ABSPATH . WPINC . '/class-wp-locale-switcher.php' );

require_once( ABSPATH . WPINC . '/functions.php' ); 

require( ABSPATH . WPINC . '/formatting.php' );
require( ABSPATH . WPINC . '/general-template.php' );

require_once( ABSPATH . WPINC . '/plugin.php' );
require( ABSPATH . WPINC . '/vars.php' );

require( ABSPATH . WPINC . '/class-wp-user.php' );
require( ABSPATH . WPINC . '/user.php' );

require( ABSPATH . WPINC . '/class-wp.php' );
require( ABSPATH . WPINC . '/class-wp-rewrite.php' );
require( ABSPATH . WPINC . '/class-wp-widget-factory.php' );
require( ABSPATH . WPINC . '/class-wp-roles.php' );

// Initialize multisite if enabled.
if ( is_multisite() ) {
	require( ABSPATH . WPINC . '/ms-blogs.php' );
	require( ABSPATH . WPINC . '/ms-settings.php' );
} elseif ( ! defined( 'MULTISITE' ) ) {
	define( 'MULTISITE', false );
}

wp_initial_constants();

wp_start_object_cache();

// Include the wpdb class and, if present, a db.php database drop-in.
global $wpdb;
require_wp_db();

$GLOBALS['table_prefix'] = $table_prefix;
wp_set_wpdb_vars();



/**
 * WordPress Object
 * @global WP $wp
 * @since 2.0.0
 */
$GLOBALS['wp'] = new WP();

$GLOBALS['wp_rewrite'] = new WP_Rewrite();

/**
 * WordPress Widget Factory Object
 * @global WP_Widget_Factory $wp_widget_factory
 * @since 2.8.0
 */
$GLOBALS['wp_widget_factory'] = new WP_Widget_Factory();

/**
 * WordPress User Roles
 * @global WP_Roles $wp_roles
 * @since 2.0.0
 */
$GLOBALS['wp_roles'] = new WP_Roles();

require( ABSPATH . WPINC . '/pluggable.php' );
wp_templating_constants();

?>