<?php
define( 'WPINC', 'wp-includes' );

require( ABSPATH . WPINC . '/load.php' );
require( ABSPATH . WPINC . '/default-constants.php' );
require_once( ABSPATH . WPINC . '/l10n.php' );
require_once( ABSPATH . WPINC . '/class-wp-locale.php' );
//require_once( ABSPATH . WPINC . '/class-wp-locale-switcher.php' );
require( ABSPATH . WPINC . '/kses.php' );

require_once( ABSPATH . WPINC . '/functions.php' ); 

require( ABSPATH . WPINC . '/formatting.php' );
require( ABSPATH . WPINC . '/general-template.php' );

require_once( ABSPATH . WPINC . '/plugin.php' );
require( ABSPATH . WPINC . '/vars.php' );

require( ABSPATH . WPINC . '/class-wp-user.php' );
require( ABSPATH . WPINC . '/user.php' );

require( ABSPATH . WPINC . '/link-template.php' );

require( ABSPATH . WPINC . '/class-wp-post-type.php' );
require( ABSPATH . WPINC . '/class-wp-post.php' );
require( ABSPATH . WPINC . '/post.php' );

require( ABSPATH . WPINC . '/class-wp.php' );
require( ABSPATH . WPINC . '/rewrite.php' );
require( ABSPATH . WPINC . '/class-wp-rewrite.php' );
require( ABSPATH . WPINC . '/class-wp-widget-factory.php' );
require( ABSPATH . WPINC . '/capabilities.php' );
require( ABSPATH . WPINC . '/class-wp-role.php' );
require( ABSPATH . WPINC . '/class-wp-roles.php' );
require( ABSPATH . WPINC . '/class-wp-theme.php' );
require( ABSPATH . WPINC . '/class-wp-error.php' );

require( ABSPATH . WPINC . '/meta.php' );
require( ABSPATH . WPINC . '/class-wp-meta-query.php' );
require( ABSPATH . WPINC . '/class-phpass.php' );

require( ABSPATH . WPINC . '/class-wp-list-util.php' );

require( ABSPATH . WPINC . '/class-http.php' );
require( ABSPATH . WPINC . '/class-wp-http-streams.php' );
require( ABSPATH . WPINC . '/class-wp-http-curl.php' );
require( ABSPATH . WPINC . '/class-wp-http-proxy.php' );
require( ABSPATH . WPINC . '/class-wp-http-cookie.php' );
require( ABSPATH . WPINC . '/class-wp-http-encoding.php' );
require( ABSPATH . WPINC . '/class-wp-http-response.php' );
require( ABSPATH . WPINC . '/class-wp-http-requests-response.php' );
require( ABSPATH . WPINC . '/class-wp-http-requests-hooks.php' );


require( ABSPATH . WPINC . '/class-wp-session-tokens.php' );
require( ABSPATH . WPINC . '/class-wp-user-meta-session-tokens.php' );

require( ABSPATH . WPINC . '/script-loader.php' );
require( ABSPATH . WPINC . '/class-wp-query.php' );
require( ABSPATH . WPINC . '/query.php' );

require( ABSPATH . WPINC . '/taxonomy.php' );
require( ABSPATH . WPINC . '/class-wp-taxonomy.php' );
require( ABSPATH . WPINC . '/class-wp-tax-query.php' );

require( ABSPATH . WPINC . '/date.php' );

require( ABSPATH . WPINC . '/template.php' );

require( ABSPATH . WPINC . '/post-template.php' );
require( ABSPATH . WPINC . '/admin-bar.php' );

require( ABSPATH . WPINC . '/post-thumbnail-template.php' );


// Initialize multisite if enabled.
if ( is_multisite() ) {
	require( ABSPATH . WPINC . '/ms-blogs.php' );
	require( ABSPATH . WPINC . '/ms-settings.php' );
} elseif ( ! defined( 'MULTISITE' ) ) {
	define( 'MULTISITE', false );
}

require( ABSPATH . WPINC . '/default-filters.php' );

wp_initial_constants();

wp_start_object_cache();

// Include the wpdb class and, if present, a db.php database drop-in.
global $wpdb;
require_wp_db();

$GLOBALS['table_prefix'] = $table_prefix;
wp_set_wpdb_vars();


require( ABSPATH . WPINC . '/theme.php' );

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

/**
 * WordPress Query object
 * @global WP_Query $wp_the_query
 * @since 2.0.0
 */
$GLOBALS['wp_the_query'] = new WP_Query();

/**
 * Holds the reference to @see $wp_the_query
 * Use this global for WordPress queries
 * @global WP_Query $wp_query
 * @since 1.5.0
 */
$GLOBALS['wp_query'] = $GLOBALS['wp_the_query'];

?>