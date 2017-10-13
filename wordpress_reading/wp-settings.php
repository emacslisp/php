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

wp_initial_constants();

wp_start_object_cache();

// Include the wpdb class and, if present, a db.php database drop-in.
global $wpdb;
require_wp_db();

$GLOBALS['table_prefix'] = $table_prefix;
wp_set_wpdb_vars();

require( ABSPATH . WPINC . '/pluggable.php' );

?>