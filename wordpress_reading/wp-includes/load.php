<?php

function require_wp_db() {
	//file_put_contents ( '/Users/ewu/output.log', print_r ( (new Exception ())->getTraceAsString (), true ) . PHP_EOL . PHP_EOL, FILE_APPEND );
	global $wpdb;
	
	require_once (ABSPATH . WPINC . '/wp-db.php');
	if (file_exists ( WP_CONTENT_DIR . '/db.php' ))
		require_once (WP_CONTENT_DIR . '/db.php');
	
	if (isset ( $wpdb )) {
		return;
	}
	
	$wpdb = new wpdb ( DB_USER, DB_PASSWORD, DB_NAME, DB_HOST );
}

function is_multisite() {
if ( defined( 'MULTISITE' ) )
	return MULTISITE;
	
	if ( defined( 'SUBDOMAIN_INSTALL' ) || defined( 'VHOST' ) || defined( 'SUNRISE' ) )
		return true;
		
		return false;
}

function wp_convert_hr_to_bytes( $value ) {
	$value = strtolower( trim( $value ) );
	$bytes = (int) $value;
	
	if ( false !== strpos( $value, 'g' ) ) {
		$bytes *= GB_IN_BYTES;
	} elseif ( false !== strpos( $value, 'm' ) ) {
		$bytes *= MB_IN_BYTES;
	} elseif ( false !== strpos( $value, 'k' ) ) {
		$bytes *= KB_IN_BYTES;
	}
	
	// Deal with large (float) values which run into the maximum integer size.
	return min( $bytes, PHP_INT_MAX );
}

function wp_is_ini_value_changeable( $setting ) {
	static $ini_all;
	
	if ( ! isset( $ini_all ) ) {
		$ini_all = false;
		// Sometimes `ini_get_all()` is disabled via the `disable_functions` option for "security purposes".
		if ( function_exists( 'ini_get_all' ) ) {
		$ini_all = ini_get_all();
		}
	}
	
	// Bit operator to workaround https://bugs.php.net/bug.php?id=44936 which changes access level to 63 in PHP 5.2.6 - 5.2.17.
	if ( isset( $ini_all[ $setting ]['access'] ) && ( INI_ALL === ( $ini_all[ $setting ]['access'] & 7 ) || INI_USER === ( $ini_all[ $setting ]['access'] & 7 ) ) ) {
		return true;
	}
	
	// If we were unable to retrieve the details, fail gracefully to assume it's changeable.
	if ( ! is_array( $ini_all ) ) {
		return true;
	}
	
	return false;
}

?>