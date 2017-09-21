<?php

function wp_load_alloptions() {
global $wpdb;

if ( ! wp_installing() || ! is_multisite() )
	$alloptions = wp_cache_get( 'alloptions', 'options' );
	else
		$alloptions = false;
		
		if ( !$alloptions ) {
			$suppress = $wpdb->suppress_errors();
			if ( !$alloptions_db = $wpdb->get_results( "SELECT option_name, option_value FROM $wpdb->options WHERE autoload = 'yes'" ) )
				$alloptions_db = $wpdb->get_results( "SELECT option_name, option_value FROM $wpdb->options" );
				$wpdb->suppress_errors($suppress);
				$alloptions = array();
				foreach ( (array) $alloptions_db as $o ) {
					$alloptions[$o->option_name] = $o->option_value;
				}
				if ( ! wp_installing() || ! is_multisite() )
					wp_cache_add( 'alloptions', $alloptions, 'options' );
		}
		
		return $alloptions;
}

?>