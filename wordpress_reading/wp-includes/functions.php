<?php 

require( ABSPATH . WPINC . '/option.php' );

function is_blog_installed() {
global $wpdb;

/*
 * Check cache first. If options table goes away and we have true
 * cached, oh well.
 */
if ( wp_cache_get( 'is_blog_installed' ) )
	return true;
	
	$suppress = $wpdb->suppress_errors();
	if ( ! wp_installing() ) {
		$alloptions = wp_load_alloptions();
	}
	// If siteurl is not set to autoload, check it specifically
	if ( !isset($alloptions) && !isset( $alloptions['siteurl'] ) )
		$installed = $wpdb->get_var( "SELECT option_value FROM $wpdb->options WHERE option_name = 'siteurl'" );
		else
			$installed = $alloptions['siteurl'];
			$wpdb->suppress_errors( $suppress );
			
			$installed = !empty( $installed );
			wp_cache_set( 'is_blog_installed', $installed );
			
			if ( $installed )
				return true;
				
				// If visiting repair.php, return true and let it take over.
				if ( defined( 'WP_REPAIRING' ) )
					return true;
					
					$suppress = $wpdb->suppress_errors();
					
					/*
					 * Loop over the WP tables. If none exist, then scratch install is allowed.
					 * If one or more exist, suggest table repair since we got here because the
					 * options table could not be accessed.
					 */
					$wp_tables = $wpdb->tables();
					foreach ( $wp_tables as $table ) {
						// The existence of custom user tables shouldn't suggest an insane state or prevent a clean install.
						if ( defined( 'CUSTOM_USER_TABLE' ) && CUSTOM_USER_TABLE == $table )
							continue;
							if ( defined( 'CUSTOM_USER_META_TABLE' ) && CUSTOM_USER_META_TABLE == $table )
								continue;
								
								if ( ! $wpdb->get_results( "DESCRIBE $table;" ) )
									continue;
									
									// One or more tables exist. We are insane.
									
									wp_load_translations_early();
									
									// Die with a DB error.
									$wpdb->error = sprintf(
											/* translators: %s: database repair URL */
											__( 'One or more database tables are unavailable. The database may need to be <a href="%s">repaired</a>.' ),
											'maint/repair.php?referrer=is_blog_installed'
											);
									
									dead_db();
					}
					
					$wpdb->suppress_errors( $suppress );
					
					wp_cache_set( 'is_blog_installed', false );
					
					return false;
}

function wp_guess_url() {
	if ( defined('WP_SITEURL') && '' != WP_SITEURL ) {
		$url = WP_SITEURL;
	} else {
		$abspath_fix = str_replace( '\\', '/', ABSPATH );
		$script_filename_dir = dirname( $_SERVER['SCRIPT_FILENAME'] );
		
		// The request is for the admin
		if ( strpos( $_SERVER['REQUEST_URI'], 'wp-admin' ) !== false || strpos( $_SERVER['REQUEST_URI'], 'wp-login.php' ) !== false ) {
			$path = preg_replace( '#/(wp-admin/.*|wp-login.php)#i', '', $_SERVER['REQUEST_URI'] );
			
			// The request is for a file in ABSPATH
		} elseif ( $script_filename_dir . '/' == $abspath_fix ) {
			// Strip off any file/query params in the path
			$path = preg_replace( '#/[^/]*$#i', '', $_SERVER['PHP_SELF'] );
			
		} else {
			if ( false !== strpos( $_SERVER['SCRIPT_FILENAME'], $abspath_fix ) ) {
				// Request is hitting a file inside ABSPATH
				$directory = str_replace( ABSPATH, '', $script_filename_dir );
				// Strip off the sub directory, and any file/query params
				$path = preg_replace( '#/' . preg_quote( $directory, '#' ) . '/[^/]*$#i', '' , $_SERVER['REQUEST_URI'] );
			} elseif ( false !== strpos( $abspath_fix, $script_filename_dir ) ) {
				// Request is hitting a file above ABSPATH
				$subdirectory = substr( $abspath_fix, strpos( $abspath_fix, $script_filename_dir ) + strlen( $script_filename_dir ) );
				// Strip off any file/query params from the path, appending the sub directory to the install
				$path = preg_replace( '#/[^/]*$#i', '' , $_SERVER['REQUEST_URI'] ) . $subdirectory;
			} else {
				$path = $_SERVER['REQUEST_URI'];
			}
		}
		
		$schema = is_ssl() ? 'https://' : 'http://'; // set_url_scheme() is not defined yet
		$url = $schema . $_SERVER['HTTP_HOST'] . $path;
	}
	
	return rtrim($url, '/');
}


?>