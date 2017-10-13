<?php

function wp_protect_special_option( $option ) {file_put_contents('/Users/ewu/output.log',print_r((new Exception)->getTraceAsString(),true). PHP_EOL . PHP_EOL,FILE_APPEND);
if ( 'alloptions' === $option || 'notoptions' === $option )
	wp_die( sprintf( __( '%s is a protected WP option and may not be modified' ), esc_html( $option ) ) );
}

function update_option( $option, $value, $autoload = null ) {
global $wpdb;

$option = trim ( $option );
if (empty ( $option ))
	return false;

wp_protect_special_option ( $option );

if (is_object ( $value ))
	$value = clone $value;

$value = sanitize_option ( $option, $value );
$old_value = get_option ( $option );

/**
 * Filters a specific option before its value is (maybe) serialized and updated.
 *
 * The dynamic portion of the hook name, `$option`, refers to the option name.
 *
 * @since 2.6.0
 * @since 4.4.0 The `$option` parameter was added.
 *       
 * @param mixed $value
 *        	The new, unserialized option value.
 * @param mixed $old_value
 *        	The old option value.
 * @param string $option
 *        	Option name.
 */
$value = apply_filters ( "pre_update_option_{$option}", $value, $old_value, $option );

/**
 * Filters an option before its value is (maybe) serialized and updated.
 *
 * @since 3.9.0
 *       
 * @param mixed $value
 *        	The new, unserialized option value.
 * @param string $option
 *        	Name of the option.
 * @param mixed $old_value
 *        	The old option value.
 */
$value = apply_filters ( 'pre_update_option', $value, $option, $old_value );

// If the new and old values are the same, no need to update.
if ($value === $old_value)
	return false;

/**
 * This filter is documented in wp-includes/option.php
 */
if (apply_filters ( 'default_option_' . $option, false, $option, false ) === $old_value) {
	// Default setting for new options is 'yes'.
	if (null === $autoload) {
		$autoload = 'yes';
	}
	
	return add_option ( $option, $value, '', $autoload );
}

$serialized_value = maybe_serialize ( $value );

/**
 * Fires immediately before an option value is updated.
 *
 * @since 2.9.0
 *       
 * @param string $option
 *        	Name of the option to update.
 * @param mixed $old_value
 *        	The old option value.
 * @param mixed $value
 *        	The new option value.
 */
do_action ( 'update_option', $option, $old_value, $value );

$update_args = array (
		'option_value' => $serialized_value 
);

if (null !== $autoload) {
	$update_args ['autoload'] = ('no' === $autoload || false === $autoload) ? 'no' : 'yes';
}

$result = $wpdb->update ( $wpdb->options, $update_args, array (
		'option_name' => $option 
) );
if (! $result)
	return false;

$notoptions = wp_cache_get ( 'notoptions', 'options' );
if (is_array ( $notoptions ) && isset ( $notoptions [$option] )) {
	unset ( $notoptions [$option] );
	wp_cache_set ( 'notoptions', $notoptions, 'options' );
}

if (! wp_installing ()) {
	$alloptions = wp_load_alloptions ();
	if (isset ( $alloptions [$option] )) {
		$alloptions [$option] = $serialized_value;
		wp_cache_set ( 'alloptions', $alloptions, 'options' );
	} else {
		wp_cache_set ( $option, $serialized_value, 'options' );
	}
}

/**
 * Fires after the value of a specific option has been successfully updated.
 *
 * The dynamic portion of the hook name, `$option`, refers to the option name.
 *
 * @since 2.0.1
 * @since 4.4.0 The `$option` parameter was added.
 *       
 * @param mixed $old_value
 *        	The old option value.
 * @param mixed $value
 *        	The new option value.
 * @param string $option
 *        	Option name.
 */
do_action ( "update_option_{$option}", $old_value, $value, $option );

/**
 * Fires after the value of an option has been successfully updated.
 *
 * @since 2.9.0
 *       
 * @param string $option
 *        	Name of the updated option.
 * @param mixed $old_value
 *        	The old option value.
 * @param mixed $value
 *        	The new option value.
 */
do_action ( 'updated_option', $option, $old_value, $value );
return true;
}

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