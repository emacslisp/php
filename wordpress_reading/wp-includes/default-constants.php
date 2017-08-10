<?php
	
function wp_initial_constants() {
	global $blog_id;
	
	/**#@+
	 * Constants for expressing human-readable data sizes in their respective number of bytes.
	 *
	 * @since 4.4.0
	 */
	define( 'KB_IN_BYTES', 1024 );
	define( 'MB_IN_BYTES', 1024 * KB_IN_BYTES );
	define( 'GB_IN_BYTES', 1024 * MB_IN_BYTES );
	define( 'TB_IN_BYTES', 1024 * GB_IN_BYTES );
	/**#@-*/
	
	$current_limit     = @ini_get( 'memory_limit' );
	$current_limit_int = wp_convert_hr_to_bytes( $current_limit );
	
	// Define memory limits.
	if ( ! defined( 'WP_MEMORY_LIMIT' ) ) {
		if ( false === wp_is_ini_value_changeable( 'memory_limit' ) ) {
			define( 'WP_MEMORY_LIMIT', $current_limit );
		} elseif ( is_multisite() ) {
			define( 'WP_MEMORY_LIMIT', '64M' );
		} else {
			define( 'WP_MEMORY_LIMIT', '40M' );
		}
	}
	
	if ( ! defined( 'WP_MAX_MEMORY_LIMIT' ) ) {
		if ( false === wp_is_ini_value_changeable( 'memory_limit' ) ) {
			define( 'WP_MAX_MEMORY_LIMIT', $current_limit );
		} elseif ( -1 === $current_limit_int || $current_limit_int > 268435456 /* = 256M */ ) {
			define( 'WP_MAX_MEMORY_LIMIT', $current_limit );
		} else {
			define( 'WP_MAX_MEMORY_LIMIT', '256M' );
		}
	}
	
	// Set memory limits.
	$wp_limit_int = wp_convert_hr_to_bytes( WP_MEMORY_LIMIT );
	if ( -1 !== $current_limit_int && ( -1 === $wp_limit_int || $wp_limit_int > $current_limit_int ) ) {
		@ini_set( 'memory_limit', WP_MEMORY_LIMIT );
	}
	
	if ( ! isset($blog_id) )
		$blog_id = 1;
		
		if ( !defined('WP_CONTENT_DIR') )
			define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' ); // no trailing slash, full paths only - WP_CONTENT_URL is defined further down
			
			// Add define('WP_DEBUG', true); to wp-config.php to enable display of notices during development.
			if ( !defined('WP_DEBUG') )
				define( 'WP_DEBUG', false );
				
				// Add define('WP_DEBUG_DISPLAY', null); to wp-config.php use the globally configured setting for
				// display_errors and not force errors to be displayed. Use false to force display_errors off.
				if ( !defined('WP_DEBUG_DISPLAY') )
					define( 'WP_DEBUG_DISPLAY', true );
					
					// Add define('WP_DEBUG_LOG', true); to enable error logging to wp-content/debug.log.
					if ( !defined('WP_DEBUG_LOG') )
						define('WP_DEBUG_LOG', false);
						
						if ( !defined('WP_CACHE') )
							define('WP_CACHE', false);
							
							// Add define('SCRIPT_DEBUG', true); to wp-config.php to enable loading of non-minified,
							// non-concatenated scripts and stylesheets.
							if ( ! defined( 'SCRIPT_DEBUG' ) ) {
								if ( ! empty( $GLOBALS['wp_version'] ) ) {
									$develop_src = false !== strpos( $GLOBALS['wp_version'], '-src' );
								} else {
									$develop_src = false;
								}
								
								define( 'SCRIPT_DEBUG', $develop_src );
							}
							
							/**
							 * Private
							 */
							if ( !defined('MEDIA_TRASH') )
								define('MEDIA_TRASH', false);
								
								if ( !defined('SHORTINIT') )
									define('SHORTINIT', false);
									
									// Constants for features added to WP that should short-circuit their plugin implementations
									define( 'WP_FEATURE_BETTER_PASSWORDS', true );
									
									/**#@+
									 * Constants for expressing human-readable intervals
									 * in their respective number of seconds.
									 *
									 * Please note that these values are approximate and are provided for convenience.
									 * For example, MONTH_IN_SECONDS wrongly assumes every month has 30 days and
									 * YEAR_IN_SECONDS does not take leap years into account.
									 *
									 * If you need more accuracy please consider using the DateTime class (https://secure.php.net/manual/en/class.datetime.php).
									 *
									 * @since 3.5.0
									 * @since 4.4.0 Introduced `MONTH_IN_SECONDS`.
									 */
									define( 'MINUTE_IN_SECONDS', 60 );
									define( 'HOUR_IN_SECONDS',   60 * MINUTE_IN_SECONDS );
									define( 'DAY_IN_SECONDS',    24 * HOUR_IN_SECONDS   );
									define( 'WEEK_IN_SECONDS',    7 * DAY_IN_SECONDS    );
									define( 'MONTH_IN_SECONDS',  30 * DAY_IN_SECONDS    );
									define( 'YEAR_IN_SECONDS',  365 * DAY_IN_SECONDS    );
									/**#@-*/
}
	
?>