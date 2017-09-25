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


function get_option( $option, $default = false ) {
global $wpdb;

$option = trim( $option );
if ( empty( $option ) )
	return false;
	
	$pre = apply_filters( "pre_option_{$option}", false, $option );
	if ( false !== $pre )
		return $pre;
		
		if ( defined( 'WP_SETUP_CONFIG' ) )
			return false;
			
			// Distinguish between `false` as a default, and not passing one.
			$passed_default = func_num_args() > 1;
			
			if ( ! wp_installing() ) {
				// prevent non-existent options from triggering multiple queries
				$notoptions = wp_cache_get( 'notoptions', 'options' );
				if ( isset( $notoptions[ $option ] ) ) {

					return apply_filters( "default_option_{$option}", $default, $option, $passed_default );
				}
				
				$alloptions = wp_load_alloptions();
				
				if ( isset( $alloptions[$option] ) ) {
					$value = $alloptions[$option];
				} else {
					$value = wp_cache_get( $option, 'options' );
					
					if ( false === $value ) {
						$row = $wpdb->get_row( $wpdb->prepare( "SELECT option_value FROM $wpdb->options WHERE option_name = %s LIMIT 1", $option ) );
						
						// Has to be get_row instead of get_var because of funkiness with 0, false, null values
						if ( is_object( $row ) ) {
							$value = $row->option_value;
							wp_cache_add( $option, $value, 'options' );
						} else { // option does not exist, so we must cache its non-existence
							if ( ! is_array( $notoptions ) ) {
								$notoptions = array();
							}
							$notoptions[$option] = true;
							wp_cache_set( 'notoptions', $notoptions, 'options' );
							
							/** This filter is documented in wp-includes/option.php */
							return apply_filters( 'default_option_' . $option, $default, $option, $passed_default );
						}
					}
				}
			} else {
				$suppress = $wpdb->suppress_errors();
				$row = $wpdb->get_row( $wpdb->prepare( "SELECT option_value FROM $wpdb->options WHERE option_name = %s LIMIT 1", $option ) );
				$wpdb->suppress_errors( $suppress );
				if ( is_object( $row ) ) {
					$value = $row->option_value;
				} else {
					/** This filter is documented in wp-includes/option.php */
					return apply_filters( 'default_option_' . $option, $default, $option, $passed_default );
				}
			}
			
			// If home is not set use siteurl.
			if ( 'home' == $option && '' == $value )
				return get_option( 'siteurl' );
				
				if ( in_array( $option, array('siteurl', 'home', 'category_base', 'tag_base') ) )
					$value = untrailingslashit( $value );
					
					return apply_filters( "option_{$option}", maybe_unserialize( $value ), $option );
}

?>