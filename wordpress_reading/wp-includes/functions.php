<?php 

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
	if ( !isset( $alloptions['siteurl'] ) )
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

?>