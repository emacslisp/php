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

function wp_load_translations_early() {
global $wp_locale;

static $loaded = false;
if ( $loaded )
	return;
	$loaded = true;
	
	if ( function_exists( 'did_action' ) && did_action( 'init' ) )
		return;
		
		// We need $wp_local_package
		require ABSPATH . WPINC . '/version.php';
		
		// Translation and localization
		require_once ABSPATH . WPINC . '/pomo/mo.php';
		require_once ABSPATH . WPINC . '/l10n.php';
		require_once ABSPATH . WPINC . '/class-wp-locale.php';
		require_once ABSPATH . WPINC . '/class-wp-locale-switcher.php';
		
		// General libraries
		require_once ABSPATH . WPINC . '/plugin.php';
		
		$locales = $locations = array();
		
		while ( true ) {
			if ( defined( 'WPLANG' ) ) {
				if ( '' == WPLANG )
					break;
					$locales[] = WPLANG;
			}
			
			if ( isset( $wp_local_package ) )
				$locales[] = $wp_local_package;
				
				if ( ! $locales )
					break;
					
					if ( defined( 'WP_LANG_DIR' ) && @is_dir( WP_LANG_DIR ) )
						$locations[] = WP_LANG_DIR;
						
						if ( defined( 'WP_CONTENT_DIR' ) && @is_dir( WP_CONTENT_DIR . '/languages' ) )
							$locations[] = WP_CONTENT_DIR . '/languages';
							
							if ( @is_dir( ABSPATH . 'wp-content/languages' ) )
								$locations[] = ABSPATH . 'wp-content/languages';
								
								if ( @is_dir( ABSPATH . WPINC . '/languages' ) )
									$locations[] = ABSPATH . WPINC . '/languages';
									
									if ( ! $locations )
										break;
										
										$locations = array_unique( $locations );
										
										foreach ( $locales as $locale ) {
											foreach ( $locations as $location ) {
												if ( file_exists( $location . '/' . $locale . '.mo' ) ) {
													load_textdomain( 'default', $location . '/' . $locale . '.mo' );
													if ( defined( 'WP_SETUP_CONFIG' ) && file_exists( $location . '/admin-' . $locale . '.mo' ) )
														load_textdomain( 'default', $location . '/admin-' . $locale . '.mo' );
														break 2;
												}
											}
										}
										
										break;
		}
		
		$wp_locale = new WP_Locale();
}

function is_admin() {
if ( isset( $GLOBALS['current_screen'] ) )
	return $GLOBALS['current_screen']->in_admin();
	elseif ( defined( 'WP_ADMIN' ) )
	return WP_ADMIN;
	
	return false;
}

function wp_using_ext_object_cache( $using = null ) {file_put_contents('/Users/ewu/output.log',print_r((new Exception)->getTraceAsString(),true). PHP_EOL . PHP_EOL,FILE_APPEND);
	global $_wp_using_ext_object_cache;
	$current_using = $_wp_using_ext_object_cache;
	if ( null !== $using )
		$_wp_using_ext_object_cache = $using;
		return $current_using;
}

function wp_installing( $is_installing = null ) {
	static $installing = null;
	
	// Support for the `WP_INSTALLING` constant, defined before WP is loaded.
	if ( is_null( $installing ) ) {
		$installing = defined( 'WP_INSTALLING' ) && WP_INSTALLING;
	}
	
	if ( ! is_null( $is_installing ) ) {
		$old_installing = $installing;
		$installing = $is_installing;
		return (bool) $old_installing;
	}
	
	return (bool) $installing;
}

function get_current_blog_id() {
	global $blog_id;
	return absint($blog_id);
}


/**
 * Whether the current request is for a site's admininstrative interface.
 *
 * e.g. `/wp-admin/`
 *
 * Does not check if the user is an administrator; current_user_can()
 * for checking roles and capabilities.
 *
 * @since 3.1.0
 *
 * @global WP_Screen $current_screen
 *
 * @return bool True if inside WordPress blog administration pages.
 */
function is_blog_admin() {
if ( isset( $GLOBALS['current_screen'] ) )
	return $GLOBALS['current_screen']->in_admin( 'site' );
	elseif ( defined( 'WP_BLOG_ADMIN' ) )
	return WP_BLOG_ADMIN;
	
	return false;
}

/**
 * Whether the current request is for the network administrative interface.
 *
 * e.g. `/wp-admin/network/`
 *
 * Does not check if the user is an administrator; current_user_can()
 * for checking roles and capabilities.
 *
 * @since 3.1.0
 *
 * @global WP_Screen $current_screen
 *
 * @return bool True if inside WordPress network administration pages.
 */
function is_network_admin() {
if ( isset( $GLOBALS['current_screen'] ) )
	return $GLOBALS['current_screen']->in_admin( 'network' );
	elseif ( defined( 'WP_NETWORK_ADMIN' ) )
	return WP_NETWORK_ADMIN;
	
	return false;
}

function wp_start_object_cache() {
		global $wp_filter;
		
		$first_init = false;
		if ( ! function_exists( 'wp_cache_init' ) ) {
		if ( file_exists( WP_CONTENT_DIR . '/object-cache.php' ) ) {
			require_once ( WP_CONTENT_DIR . '/object-cache.php' );
			if ( function_exists( 'wp_cache_init' ) ) {
			wp_using_ext_object_cache( true );
			}
			
			// Re-initialize any hooks added manually by object-cache.php
			if ( $wp_filter ) {
				$wp_filter = WP_Hook::build_preinitialized_hooks( $wp_filter );
			}
		}
		
		$first_init = true;
		} elseif ( ! wp_using_ext_object_cache() && file_exists( WP_CONTENT_DIR . '/object-cache.php' ) ) {
			/*
			 * Sometimes advanced-cache.php can load object-cache.php before
			 * it is loaded here. This breaks the function_exists check above
			 * and can result in `$_wp_using_ext_object_cache` being set
			 * incorrectly. Double check if an external cache exists.
			 */
			wp_using_ext_object_cache( true );
		}
		
		if ( ! wp_using_ext_object_cache() ) {
			require_once ( ABSPATH . WPINC . '/cache.php' );
		}
		
		/*
		 * If cache supports reset, reset instead of init if already
		 * initialized. Reset signals to the cache that global IDs
		 * have changed and it may need to update keys and cleanup caches.
		 */
		if ( ! $first_init && function_exists( 'wp_cache_switch_to_blog' ) ) {
		wp_cache_switch_to_blog( get_current_blog_id() );
		} elseif ( function_exists( 'wp_cache_init' ) ) {
		wp_cache_init();
		}
		
		if ( function_exists( 'wp_cache_add_global_groups' ) ) {
		wp_cache_add_global_groups( array( 'users', 'userlogins', 'usermeta', 'user_meta', 'useremail', 'userslugs', 'site-transient', 'site-options', 'site-lookup', 'blog-lookup', 'blog-details', 'site-details', 'rss', 'global-posts', 'blog-id-cache', 'networks', 'sites' ) );
		wp_cache_add_non_persistent_groups( array( 'counts', 'plugins' ) );
		}
}

function get_current_network_id() {
if ( ! is_multisite() ) {
	return 1;
}

$current_network = get_network();

if ( ! isset( $current_network->id ) ) {
	return get_main_network_id();
}

return absint( $current_network->id );
}

function wp_set_wpdb_vars() {
global $wpdb, $table_prefix;
if ( !empty( $wpdb->error ) )
	dead_db();
	
	$wpdb->field_types = array( 'post_author' => '%d', 'post_parent' => '%d', 'menu_order' => '%d', 'term_id' => '%d', 'term_group' => '%d', 'term_taxonomy_id' => '%d',
			'parent' => '%d', 'count' => '%d','object_id' => '%d', 'term_order' => '%d', 'ID' => '%d', 'comment_ID' => '%d', 'comment_post_ID' => '%d', 'comment_parent' => '%d',
			'user_id' => '%d', 'link_id' => '%d', 'link_owner' => '%d', 'link_rating' => '%d', 'option_id' => '%d', 'blog_id' => '%d', 'meta_id' => '%d', 'post_id' => '%d',
			'user_status' => '%d', 'umeta_id' => '%d', 'comment_karma' => '%d', 'comment_count' => '%d',
			// multisite:
			'active' => '%d', 'cat_id' => '%d', 'deleted' => '%d', 'lang_id' => '%d', 'mature' => '%d', 'public' => '%d', 'site_id' => '%d', 'spam' => '%d',
	);
	
	$prefix = $wpdb->set_prefix( $table_prefix );
	
	if ( is_wp_error( $prefix ) ) {
		wp_load_translations_early();
		wp_die(
				/* translators: 1: $table_prefix 2: wp-config.php */
				sprintf( __( '<strong>ERROR</strong>: %1$s in %2$s can only contain numbers, letters, and underscores.' ),
						'<code>$table_prefix</code>',
						'<code>wp-config.php</code>'
						)
				);
	}
}

function is_wp_error( $thing ) {
	return ( $thing instanceof WP_Error );
}

function is_ssl() {
	if ( isset( $_SERVER['HTTPS'] ) ) {
		if ( 'on' == strtolower( $_SERVER['HTTPS'] ) ) {
			return true;
		}
		
		if ( '1' == $_SERVER['HTTPS'] ) {
			return true;
		}
	} elseif ( isset($_SERVER['SERVER_PORT'] ) && ( '443' == $_SERVER['SERVER_PORT'] ) ) {
		return true;
	}
	return false;
}

?>