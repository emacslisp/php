<?php

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}


error_reporting( E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_ERROR | E_WARNING | E_PARSE | E_USER_ERROR | E_USER_WARNING | E_RECOVERABLE_ERROR );

if ( file_exists( ABSPATH . 'wp-config.php') ) {
	
	/** The config file resides in ABSPATH */
	require_once( ABSPATH . 'wp-config.php' );
	
} elseif ( @file_exists( dirname( ABSPATH ) . '/wp-config.php' ) && ! @file_exists( dirname( ABSPATH ) . '/wp-settings.php' ) ) {
	
	/** The config file resides one level above ABSPATH but is not part of another install */
	require_once( dirname( ABSPATH ) . '/wp-config.php' );
	
} else {
	
	// A config file doesn't exist
	
	define( 'WPINC', 'wp-includes' );
	require_once( ABSPATH . WPINC . '/load.php' );
	
	// Standardize $_SERVER variables across setups.
	wp_fix_server_vars();
	
	require_once( ABSPATH . WPINC . '/functions.php' );
	
	$path = wp_guess_url() . '/wp-admin/setup-config.php';
	
	/*
	 * We're going to redirect to setup-config.php. While this shouldn't result
	 * in an infinite loop, that's a silly thing to assume, don't you think? If
	 * we're traveling in circles, our last-ditch effort is "Need more help?"
	 */
	if ( false === strpos( $_SERVER['REQUEST_URI'], 'setup-config' ) ) {
		header( 'Location: ' . $path );
		exit;
	}
	
	define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
	require_once( ABSPATH . WPINC . '/version.php' );
	
	wp_check_php_mysql_versions();
	wp_load_translations_early();
	
	// Die with an error message
	$die  = sprintf(
			/* translators: %s: wp-config.php */
			__( "There doesn't seem to be a %s file. I need this before we can get started." ),
			'<code>wp-config.php</code>'
			) . '</p>';
			$die .= '<p>' . sprintf(
					/* translators: %s: Codex URL */
					__( "Need more help? <a href='%s'>We got it</a>." ),
					__( 'https://codex.wordpress.org/Editing_wp-config.php' )
					) . '</p>';
					$die .= '<p>' . sprintf(
							/* translators: %s: wp-config.php */
							__( "You can create a %s file through a web interface, but this doesn't work for all server setups. The safest way is to manually create the file." ),
							'<code>wp-config.php</code>'
							) . '</p>';
							$die .= '<p><a href="' . $path . '" class="button button-large">' . __( "Create a Configuration File" ) . '</a>';
							
							wp_die( $die, __( 'WordPress &rsaquo; Error' ) );
}
?>