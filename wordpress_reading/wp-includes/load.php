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


function wp_fix_server_vars() {
global $PHP_SELF;

$default_server_values = array(
		'SERVER_SOFTWARE' => '',
		'REQUEST_URI' => '',
);

$_SERVER = array_merge( $default_server_values, $_SERVER );

// Fix for IIS when running with PHP ISAPI
if ( empty( $_SERVER['REQUEST_URI'] ) || ( PHP_SAPI != 'cgi-fcgi' && preg_match( '/^Microsoft-IIS\//', $_SERVER['SERVER_SOFTWARE'] ) ) ) {
	
	// IIS Mod-Rewrite
	if ( isset( $_SERVER['HTTP_X_ORIGINAL_URL'] ) ) {
		$_SERVER['REQUEST_URI'] = $_SERVER['HTTP_X_ORIGINAL_URL'];
	}
	// IIS Isapi_Rewrite
	elseif ( isset( $_SERVER['HTTP_X_REWRITE_URL'] ) ) {
		$_SERVER['REQUEST_URI'] = $_SERVER['HTTP_X_REWRITE_URL'];
	} else {
		// Use ORIG_PATH_INFO if there is no PATH_INFO
		if ( !isset( $_SERVER['PATH_INFO'] ) && isset( $_SERVER['ORIG_PATH_INFO'] ) )
			$_SERVER['PATH_INFO'] = $_SERVER['ORIG_PATH_INFO'];
			
			// Some IIS + PHP configurations puts the script-name in the path-info (No need to append it twice)
			if ( isset( $_SERVER['PATH_INFO'] ) ) {
				if ( $_SERVER['PATH_INFO'] == $_SERVER['SCRIPT_NAME'] )
					$_SERVER['REQUEST_URI'] = $_SERVER['PATH_INFO'];
					else
						$_SERVER['REQUEST_URI'] = $_SERVER['SCRIPT_NAME'] . $_SERVER['PATH_INFO'];
			}
			
			// Append the query string if it exists and isn't null
			if ( ! empty( $_SERVER['QUERY_STRING'] ) ) {
				$_SERVER['REQUEST_URI'] .= '?' . $_SERVER['QUERY_STRING'];
			}
	}
}

// Fix for PHP as CGI hosts that set SCRIPT_FILENAME to something ending in php.cgi for all requests
if ( isset( $_SERVER['SCRIPT_FILENAME'] ) && ( strpos( $_SERVER['SCRIPT_FILENAME'], 'php.cgi' ) == strlen( $_SERVER['SCRIPT_FILENAME'] ) - 7 ) )
	$_SERVER['SCRIPT_FILENAME'] = $_SERVER['PATH_TRANSLATED'];
	
	// Fix for Dreamhost and other PHP as CGI hosts
	if ( strpos( $_SERVER['SCRIPT_NAME'], 'php.cgi' ) !== false )
		unset( $_SERVER['PATH_INFO'] );
		
		// Fix empty PHP_SELF
		$PHP_SELF = $_SERVER['PHP_SELF'];
		if ( empty( $PHP_SELF ) )
			$_SERVER['PHP_SELF'] = $PHP_SELF = preg_replace( '/(\?.*)?$/', '', $_SERVER["REQUEST_URI"] );
}

function wp_check_php_mysql_versions() {
	global $required_php_version, $wp_version;
	$php_version = phpversion ();
	
	if (version_compare ( $required_php_version, $php_version, '>' )) {
		wp_load_translations_early ();
		
		$protocol = wp_get_server_protocol ();
		header ( sprintf ( '%s 500 Internal Server Error', $protocol ), true, 500 );
		header ( 'Content-Type: text/html; charset=utf-8' );
		/* translators: 1: Current PHP version number, 2: WordPress version number, 3: Minimum required PHP version number */
		die ( sprintf ( __ ( 'Your server is running PHP version %1$s but WordPress %2$s requires at least %3$s.' ), $php_version, $wp_version, $required_php_version ) );
	}
	
	if (! extension_loaded ( 'mysql' ) && ! extension_loaded ( 'mysqli' ) && ! extension_loaded ( 'mysqlnd' ) && ! file_exists ( WP_CONTENT_DIR . '/db.php' )) {
		wp_load_translations_early ();
		
		$protocol = wp_get_server_protocol ();
		header ( sprintf ( '%s 500 Internal Server Error', $protocol ), true, 500 );
		header ( 'Content-Type: text/html; charset=utf-8' );
		die ( __ ( 'Your PHP installation appears to be missing the MySQL extension which is required by WordPress.' ) );
	}
}
function wp_get_server_protocol() {
	$protocol = $_SERVER ['SERVER_PROTOCOL'];
	if (! in_array ( $protocol, array (
			'HTTP/1.1',
			'HTTP/2',
			'HTTP/2.0' 
	) )) {
		$protocol = 'HTTP/1.0';
	}
	return $protocol;
}

?>