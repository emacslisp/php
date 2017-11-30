<?php
require (ABSPATH . WPINC . '/option.php');
function is_blog_installed() {
	global $wpdb;
	
	/*
	 * Check cache first. If options table goes away and we have true
	 * cached, oh well.
	 */
	if (wp_cache_get ( 'is_blog_installed' ))
		return true;
	
	$suppress = $wpdb->suppress_errors ();
	if (! wp_installing ()) {
		$alloptions = wp_load_alloptions ();
	}
	// If siteurl is not set to autoload, check it specifically
	if (isset ( $alloptions ) && ! isset ( $alloptions ['siteurl'] ))
		$installed = $wpdb->get_var ( "SELECT option_value FROM $wpdb->options WHERE option_name = 'siteurl'" );
	else
		$installed = $alloptions ['siteurl'];
	$wpdb->suppress_errors ( $suppress );
	
	$installed = ! empty ( $installed );
	wp_cache_set ( 'is_blog_installed', $installed );
	
	if ($installed)
		return true;
	
	// If visiting repair.php, return true and let it take over.
	if (defined ( 'WP_REPAIRING' ))
		return true;
	
	$suppress = $wpdb->suppress_errors ();
	
	/*
	 * Loop over the WP tables. If none exist, then scratch install is allowed.
	 * If one or more exist, suggest table repair since we got here because the
	 * options table could not be accessed.
	 */
	$wp_tables = $wpdb->tables ();
	foreach ( $wp_tables as $table ) {
		// The existence of custom user tables shouldn't suggest an insane state or prevent a clean install.
		if (defined ( 'CUSTOM_USER_TABLE' ) && CUSTOM_USER_TABLE == $table)
			continue;
		if (defined ( 'CUSTOM_USER_META_TABLE' ) && CUSTOM_USER_META_TABLE == $table)
			continue;
		
		if (! $wpdb->get_results ( "DESCRIBE $table;" ))
			continue;
		
		// One or more tables exist. We are insane.
		
		wp_load_translations_early ();
		
		// Die with a DB error.
		$wpdb->error = sprintf(
											/* translators: %s: database repair URL */
											__ ( 'One or more database tables are unavailable. The database may need to be <a href="%s">repaired</a>.' ), 'maint/repair.php?referrer=is_blog_installed' );
		
		dead_db ();
	}
	
	$wpdb->suppress_errors ( $suppress );
	
	wp_cache_set ( 'is_blog_installed', false );
	
	return false;
}


/**
 * Load custom DB error or display WordPress DB error.
 *
 * If a file exists in the wp-content directory named db-error.php, then it will
 * be loaded instead of displaying the WordPress DB error. If it is not found,
 * then the WordPress DB error will be displayed instead.
 *
 * The WordPress DB error sets the HTTP status header to 500 to try to prevent
 * search engines from caching the message. Custom DB messages should do the
 * same.
 *
 * This function was backported to WordPress 2.3.2, but originally was added
 * in WordPress 2.5.0.
 *
 * @since 2.3.2
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 */
function dead_db() {
global $wpdb;

wp_load_translations_early();

// Load custom DB error template, if present.
if ( file_exists( WP_CONTENT_DIR . '/db-error.php' ) ) {
	require_once( WP_CONTENT_DIR . '/db-error.php' );
	die();
}

// If installing or in the admin, provide the verbose message.
if ( wp_installing() || defined( 'WP_ADMIN' ) )
	wp_die($wpdb->error);
	
	// Otherwise, be terse.
	status_header( 500 );
	nocache_headers();
	header( 'Content-Type: text/html; charset=utf-8' );
	?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml"<?php if ( is_rtl() ) echo ' dir="rtl"'; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php _e( 'Database Error' ); ?></title>

</head>
<body>
	<h1><?php _e( 'Error establishing a database connection' ); ?></h1>
</body>
</html>
<?php
	die();
}


function _doing_it_wrong( $function, $message, $version ) {

/**
 * Fires when the given function is being used incorrectly.
 *
 * @since 3.1.0
 *
 * @param string $function The function that was called.
 * @param string $message  A message explaining what has been done incorrectly.
 * @param string $version  The version of WordPress where the message was added.
 */
do_action( 'doing_it_wrong_run', $function, $message, $version );

/**
 * Filters whether to trigger an error for _doing_it_wrong() calls.
 *
 * @since 3.1.0
 *
 * @param bool $trigger Whether to trigger the error for _doing_it_wrong() calls. Default true.
 */
if ( WP_DEBUG && apply_filters( 'doing_it_wrong_trigger_error', true ) ) {
	if ( function_exists( '__' ) ) {
	if ( is_null( $version ) ) {
		$version = '';
	} else {
		/* translators: %s: version number */
		$version = sprintf( __( '(This message was added in version %s.)' ), $version );
	}
	/* translators: %s: Codex URL */
	$message .= ' ' . sprintf( __( 'Please see <a href="%s">Debugging in WordPress</a> for more information.' ),
			__( 'https://codex.wordpress.org/Debugging_in_WordPress' )
			);
	/* translators: Developer debugging message. 1: PHP function name, 2: Explanatory message, 3: Version information message */
	trigger_error( sprintf( __( '%1$s was called <strong>incorrectly</strong>. %2$s %3$s' ), $function, $message, $version ) );
	} else {
		if ( is_null( $version ) ) {
			$version = '';
		} else {
			$version = sprintf( '(This message was added in version %s.)', $version );
		}
		$message .= sprintf( ' Please see <a href="%s">Debugging in WordPress</a> for more information.',
				'https://codex.wordpress.org/Debugging_in_WordPress'
				);
		trigger_error( sprintf( '%1$s was called <strong>incorrectly</strong>. %2$s %3$s', $function, $message, $version ) );
	}
}
}

function current_time( $type, $gmt = 0 ) {
switch ( $type ) {
	case 'mysql':
		return ( $gmt ) ? gmdate( 'Y-m-d H:i:s' ) : gmdate( 'Y-m-d H:i:s', ( time() + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ) ) );
	case 'timestamp':
		return ( $gmt ) ? time() : time() + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS );
	default:
		return ( $gmt ) ? date( $type ) : date( $type, time() + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ) );
}
}

function force_ssl_admin( $force = null ) {
static $forced = false;

if ( !is_null( $force ) ) {
	$old_forced = $forced;
	$forced = $force;
	return $old_forced;
}

return $forced;
}

/**
 * Serialize data, if needed.
 *
 * @since 2.0.5
 *
 * @param string|array|object $data Data that might be serialized.
 * @return mixed A scalar data
 */
function maybe_serialize( $data ) {
if ( is_array( $data ) || is_object( $data ) )
	return serialize( $data );
	
	// Double serialization is required for backward compatibility.
	// See https://core.trac.wordpress.org/ticket/12930
	// Also the world will end. See WP 3.6.1.
	if ( is_serialized( $data, false ) )
		return serialize( $data );
		
		return $data;
}

function _cleanup_header_comment( $str ) {
	return trim(preg_replace("/\s*(?:\*\/|\?>).*/", '', $str));
}

function wp_suspend_cache_addition( $suspend = null ) {
static $_suspend = false;

if ( is_bool( $suspend ) )
	$_suspend = $suspend;
	
	return $_suspend;
}

function is_serialized($data, $strict = true) {
	// if it isn't a string, it isn't serialized.
	if (! is_string ( $data )) {
		return false;
	}
	$data = trim ( $data );
	if ('N;' == $data) {
		return true;
	}
	if (strlen ( $data ) < 4) {
		return false;
	}
	if (':' !== $data [1]) {
		return false;
	}
	if ($strict) {
		$lastc = substr ( $data, - 1 );
		if (';' !== $lastc && '}' !== $lastc) {
			return false;
		}
	} else {
		$semicolon = strpos ( $data, ';' );
		$brace = strpos ( $data, '}' );
		// Either ; or } must exist.
		if (false === $semicolon && false === $brace)
			return false;
		// But neither must be in the first X characters.
		if (false !== $semicolon && $semicolon < 3)
			return false;
		if (false !== $brace && $brace < 4)
			return false;
	}
	$token = $data [0];
	switch ($token) {
		case 's' :
			if ($strict) {
				if ('"' !== substr ( $data, - 2, 1 )) {
					return false;
				}
			} elseif (false === strpos ( $data, '"' )) {
				return false;
			}
		// or else fall through
		case 'a' :
		case 'O' :
			return ( bool ) preg_match ( "/^{$token}:[0-9]+:/s", $data );
		case 'b' :
		case 'i' :
		case 'd' :
			$end = $strict ? '$' : '';
			return ( bool ) preg_match ( "/^{$token}:[0-9.E-]+;$end/", $data );
	}
	return false;
}

function maybe_unserialize( $original ) {
if ( is_serialized( $original ) ) // don't attempt to unserialize data that wasn't serialized going in
	return @unserialize( $original );
	return $original;
}

/**
 * Determine whether a site is the main site of the current network.
 *
 * @since 3.0.0
 *
 * @param int $site_id Optional. Site ID to test. Defaults to current site.
 * @return bool True if $site_id is the main site of the network, or if not
 *              running Multisite.
 */
function is_main_site( $site_id = null ) {
if ( ! is_multisite() )
	return true;
	
	if ( ! $site_id )
		$site_id = get_current_blog_id();
		
		return (int) $site_id === (int) get_network()->site_id;
}

function get_file_data($file, $default_headers, $context = '') {
	// We don't need to write to the file, so just open for reading.
	$fp = fopen ( $file, 'r' );
	
	// Pull only the first 8kiB of the file in.
	$file_data = fread ( $fp, 8192 );
	
	// PHP will close file handle, but we are good citizens.
	fclose ( $fp );
	
	// Make sure we catch CR-only line endings.
	$file_data = str_replace ( "\r", "\n", $file_data );
	
	/**
	 * Filters extra file headers by context.
	 *
	 * The dynamic portion of the hook name, `$context`, refers to
	 * the context where extra headers might be loaded.
	 *
	 * @since 2.9.0
	 *       
	 * @param array $extra_context_headers
	 *        	Empty array by default.
	 */
	if ($context && $extra_headers = apply_filters ( "extra_{$context}_headers", array () )) {
		$extra_headers = array_combine ( $extra_headers, $extra_headers ); // keys equal values
		$all_headers = array_merge ( $extra_headers, ( array ) $default_headers );
	} else {
		$all_headers = $default_headers;
	}
	
	foreach ( $all_headers as $field => $regex ) {
		if (preg_match ( '/^[ \t\/*#@]*' . preg_quote ( $regex, '/' ) . ':(.*)$/mi', $file_data, $match ) && $match [1])
			$all_headers [$field] = _cleanup_header_comment ( $match [1] );
		else
			$all_headers [$field] = '';
	}
	
	return $all_headers;
}
function reset_mbstring_encoding() {
	mbstring_binary_safe_encoding ( true );
}
function wp_guess_url() {
	if (defined ( 'WP_SITEURL' ) && '' != WP_SITEURL) {
		$url = WP_SITEURL;
	} else {
		$abspath_fix = str_replace ( '\\', '/', ABSPATH );
		$script_filename_dir = dirname ( $_SERVER ['SCRIPT_FILENAME'] );
		
		// The request is for the admin
		if (strpos ( $_SERVER ['REQUEST_URI'], 'wp-admin' ) !== false || strpos ( $_SERVER ['REQUEST_URI'], 'wp-login.php' ) !== false) {
			$path = preg_replace ( '#/(wp-admin/.*|wp-login.php)#i', '', $_SERVER ['REQUEST_URI'] );
			
			// The request is for a file in ABSPATH
		} elseif ($script_filename_dir . '/' == $abspath_fix) {
			// Strip off any file/query params in the path
			$path = preg_replace ( '#/[^/]*$#i', '', $_SERVER ['PHP_SELF'] );
		} else {
			if (false !== strpos ( $_SERVER ['SCRIPT_FILENAME'], $abspath_fix )) {
				// Request is hitting a file inside ABSPATH
				$directory = str_replace ( ABSPATH, '', $script_filename_dir );
				// Strip off the sub directory, and any file/query params
				$path = preg_replace ( '#/' . preg_quote ( $directory, '#' ) . '/[^/]*$#i', '', $_SERVER ['REQUEST_URI'] );
			} elseif (false !== strpos ( $abspath_fix, $script_filename_dir )) {
				// Request is hitting a file above ABSPATH
				$subdirectory = substr ( $abspath_fix, strpos ( $abspath_fix, $script_filename_dir ) + strlen ( $script_filename_dir ) );
				// Strip off any file/query params from the path, appending the sub directory to the install
				$path = preg_replace ( '#/[^/]*$#i', '', $_SERVER ['REQUEST_URI'] ) . $subdirectory;
			} else {
				$path = $_SERVER ['REQUEST_URI'];
			}
		}
		
		$schema = is_ssl () ? 'https://' : 'http://'; // set_url_scheme() is not defined yet
		$url = $schema . $_SERVER ['HTTP_HOST'] . $path;
	}
	
	return rtrim ( $url, '/' );
}

function global_terms_enabled() {
if ( ! is_multisite() )
	return false;
	
	static $global_terms = null;
	if ( is_null( $global_terms ) ) {
		
		/**
		 * Filters whether global terms are enabled.
		 *
		 * Passing a non-null value to the filter will effectively short-circuit the function,
		 * returning the value of the 'global_terms_enabled' site option instead.
		 *
		 * @since 3.0.0
		 *
		 * @param null $enabled Whether global terms are enabled.
		 */
		$filter = apply_filters( 'global_terms_enabled', null );
		if ( ! is_null( $filter ) )
			$global_terms = (bool) $filter;
			else
				$global_terms = (bool) get_site_option( 'global_terms_enabled', false );
	}
	return $global_terms;
}

/**
 * Retrieve a list of protocols to allow in HTML attributes.
 *
 * @since 3.3.0
 * @since 4.3.0 Added 'webcal' to the protocols array.
 * @since 4.7.0 Added 'urn' to the protocols array.
 *
 * @see wp_kses()
 * @see esc_url()
 *
 * @staticvar array $protocols
 *
 * @return array Array of allowed protocols. Defaults to an array containing 'http', 'https',
 *               'ftp', 'ftps', 'mailto', 'news', 'irc', 'gopher', 'nntp', 'feed', 'telnet',
 *               'mms', 'rtsp', 'svn', 'tel', 'fax', 'xmpp', 'webcal', and 'urn'.
 */
function wp_allowed_protocols() {
static $protocols = array();

if ( empty( $protocols ) ) {
	$protocols = array( 'http', 'https', 'ftp', 'ftps', 'mailto', 'news', 'irc', 'gopher', 'nntp', 'feed', 'telnet', 'mms', 'rtsp', 'svn', 'tel', 'fax', 'xmpp', 'webcal', 'urn' );
	
	/**
	 * Filters the list of protocols allowed in HTML attributes.
	 *
	 * @since 3.0.0
	 *
	 * @param array $protocols Array of allowed protocols e.g. 'http', 'ftp', 'tel', and more.
	 */
	$protocols = apply_filters( 'kses_allowed_protocols', $protocols );
}

return $protocols;
}

function wp_parse_args($args, $defaults = '') {
	if (is_object ( $args ))
		$r = get_object_vars ( $args );
	elseif (is_array ( $args ))
		$r = & $args;
	else
		wp_parse_str ( $args, $r );
	
	if (is_array ( $defaults ))
		return array_merge ( $defaults, $r );
	return $r;
}
function mbstring_binary_safe_encoding($reset = false) {
	static $encodings = array ();
	static $overloaded = null;
	
	if (is_null ( $overloaded ))
		$overloaded = function_exists ( 'mb_internal_encoding' ) && (ini_get ( 'mbstring.func_overload' ) & 2);
	
	if (false === $overloaded)
		return;
	
	if (! $reset) {
		$encoding = mb_internal_encoding ();
		array_push ( $encodings, $encoding );
		mb_internal_encoding ( 'ISO-8859-1' );
	}
	
	if ($reset && $encodings) {
		$encoding = array_pop ( $encodings );
		mb_internal_encoding ( $encoding );
	}
}
function nocache_headers() {
	$headers = wp_get_nocache_headers ();
	
	unset ( $headers ['Last-Modified'] );
	
	// In PHP 5.3+, make sure we are not sending a Last-Modified header.
	if (function_exists ( 'header_remove' )) {
		@header_remove ( 'Last-Modified' );
	} else {
		// In PHP 5.2, send an empty Last-Modified header, but only as a
		// last resort to override a header already sent. #WP23021
		foreach ( headers_list () as $header ) {
			if (0 === stripos ( $header, 'Last-Modified' )) {
				$headers ['Last-Modified'] = '';
				break;
			}
		}
	}
	
	foreach ( $headers as $name => $field_value )
		@header ( "{$name}: {$field_value}" );
}
function absint($maybeint) {
	return abs ( intval ( $maybeint ) );
}
function wp_get_nocache_headers() {
	$headers = array (
			'Expires' => 'Wed, 11 Jan 1984 05:00:00 GMT',
			'Cache-Control' => 'no-cache, must-revalidate, max-age=0' 
	);
	
	if (function_exists ( 'apply_filters' )) {
		
		$headers = ( array ) apply_filters ( 'nocache_headers', $headers );
	}
	$headers ['Last-Modified'] = false;
	return $headers;
}

/**
 * Mark a function argument as deprecated and inform when it has been used.
 *
 * This function is to be used whenever a deprecated function argument is used.
 * Before this function is called, the argument must be checked for whether it was
 * used by comparing it to its default value or evaluating whether it is empty.
 * For example:
 *
 *     if ( ! empty( $deprecated ) ) {
 *         _deprecated_argument( __FUNCTION__, '3.0.0' );
 *     }
 *
 *
 * There is a hook deprecated_argument_run that will be called that can be used
 * to get the backtrace up to what file and function used the deprecated
 * argument.
 *
 * The current behavior is to trigger a user error if WP_DEBUG is true.
 *
 * @since 3.0.0
 * @access private
 *
 * @param string $function The function that was called.
 * @param string $version  The version of WordPress that deprecated the argument used.
 * @param string $message  Optional. A message regarding the change. Default null.
 */
function _deprecated_argument( $function, $version, $message = null ) {file_put_contents('/Users/ewu/output.log',print_r((new Exception)->getTraceAsString(),true). PHP_EOL . PHP_EOL,FILE_APPEND);

/**
 * Fires when a deprecated argument is called.
 *
 * @since 3.0.0
 *
 * @param string $function The function that was called.
 * @param string $message  A message regarding the change.
 * @param string $version  The version of WordPress that deprecated the argument used.
 */
do_action( 'deprecated_argument_run', $function, $message, $version );

/**
 * Filters whether to trigger an error for deprecated arguments.
 *
 * @since 3.0.0
 *
 * @param bool $trigger Whether to trigger the error for deprecated arguments. Default true.
 */
if ( WP_DEBUG && apply_filters( 'deprecated_argument_trigger_error', true ) ) {
	if ( function_exists( '__' ) ) {
	if ( ! is_null( $message ) ) {
		/* translators: 1: PHP function name, 2: version number, 3: optional message regarding the change */
		trigger_error( sprintf( __('%1$s was called with an argument that is <strong>deprecated</strong> since version %2$s! %3$s'), $function, $version, $message ) );
	} else {
		/* translators: 1: PHP function name, 2: version number */
		trigger_error( sprintf( __('%1$s was called with an argument that is <strong>deprecated</strong> since version %2$s with no alternative available.'), $function, $version ) );
	}
	} else {
		if ( ! is_null( $message ) ) {
			trigger_error( sprintf( '%1$s was called with an argument that is <strong>deprecated</strong> since version %2$s! %3$s', $function, $version, $message ) );
		} else {
			trigger_error( sprintf( '%1$s was called with an argument that is <strong>deprecated</strong> since version %2$s with no alternative available.', $function, $version ) );
		}
	}
}
}

function is_main_network($network_id = null) {
	file_put_contents ( '/Users/ewu/output.log', print_r ( (new Exception ())->getTraceAsString (), true ) . PHP_EOL . PHP_EOL, FILE_APPEND );
	if (! is_multisite ()) {
		return true;
	}
	
	if (null === $network_id) {
		$network_id = get_current_network_id ();
	}
	
	$network_id = ( int ) $network_id;
	
	return ($network_id === get_main_network_id ());
}

function wp_cache_get_last_changed( $group ) {
$last_changed = wp_cache_get( 'last_changed', $group );

if ( ! $last_changed ) {
	$last_changed = microtime();
	wp_cache_set( 'last_changed', $last_changed, $group );
}

return $last_changed;
}

function wp_filter_object_list( $list, $args = array(), $operator = 'and', $field = false ) {
if ( ! is_array( $list ) ) {
	return array();
}

$util = new WP_List_Util( $list );

$util->filter( $args, $operator );

if ( $field ) {
	$util->pluck( $field );
}

return $util->get_output();
}

/**
 * Get the main network ID.
 *
 * @since 4.3.0
 *       
 * @return int The ID of the main network.
 */
function get_main_network_id() {
	if (! is_multisite ()) {
		return 1;
	}
	
	$current_network = get_network ();
	
	if (defined ( 'PRIMARY_NETWORK_ID' )) {
		$main_network_id = PRIMARY_NETWORK_ID;
	} elseif (isset ( $current_network->id ) && 1 === ( int ) $current_network->id) {
		// If the current network has an ID of 1, assume it is the main network.
		$main_network_id = 1;
	} else {
		$_networks = get_networks ( array (
				'fields' => 'ids',
				'number' => 1 
		) );
		$main_network_id = array_shift ( $_networks );
	}
	
	/**
	 * Filters the main network ID.
	 *
	 * @since 4.3.0
	 *       
	 * @param int $main_network_id
	 *        	The ID of the main network.
	 */
	return ( int ) apply_filters ( 'get_main_network_id', $main_network_id );
}

?>