<?php

function wp_protect_special_option( $option ) {
if ( 'alloptions' === $option || 'notoptions' === $option )
	wp_die( sprintf( __( '%s is a protected WP option and may not be modified' ), esc_html( $option ) ) );
}

/**
 * Add a new option.
 *
 * You do not need to serialize values. If the value needs to be serialized, then
 * it will be serialized before it is inserted into the database. Remember,
 * resources can not be serialized or added as an option.
 *
 * You can create options without values and then update the values later.
 * Existing options will not be updated and checks are performed to ensure that you
 * aren't adding a protected WordPress option. Care should be taken to not name
 * options the same as the ones which are protected.
 *
 * @since 1.0.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param string         $option      Name of option to add. Expected to not be SQL-escaped.
 * @param mixed          $value       Optional. Option value. Must be serializable if non-scalar. Expected to not be SQL-escaped.
 * @param string         $deprecated  Optional. Description. Not used anymore.
 * @param string|bool    $autoload    Optional. Whether to load the option when WordPress starts up.
 *                                    Default is enabled. Accepts 'no' to disable for legacy reasons.
 * @return bool False if option was not added and true if option was added.
 */
function add_option( $option, $value = '', $deprecated = '', $autoload = 'yes' ) {
global $wpdb;

if ( !empty( $deprecated ) )
	_deprecated_argument( __FUNCTION__, '2.3.0' );
	
	$option = trim($option);
	if ( empty($option) )
		return false;
		
		wp_protect_special_option( $option );
		
		if ( is_object($value) )
			$value = clone $value;
			
			$value = sanitize_option( $option, $value );
			
			// Make sure the option doesn't already exist. We can check the 'notoptions' cache before we ask for a db query
			$notoptions = wp_cache_get( 'notoptions', 'options' );
			if ( !is_array( $notoptions ) || !isset( $notoptions[$option] ) )
				/** This filter is documented in wp-includes/option.php */
				if ( apply_filters( 'default_option_' . $option, false, $option, false ) !== get_option( $option ) )
					return false;
					
					$serialized_value = maybe_serialize( $value );
					$autoload = ( 'no' === $autoload || false === $autoload ) ? 'no' : 'yes';
					
					/**
					 * Fires before an option is added.
					 *
					 * @since 2.9.0
					 *
					 * @param string $option Name of the option to add.
					 * @param mixed  $value  Value of the option.
					 */
					do_action( 'add_option', $option, $value );
					
					$result = $wpdb->query( $wpdb->prepare( "INSERT INTO `$wpdb->options` (`option_name`, `option_value`, `autoload`) VALUES (%s, %s, %s) ON DUPLICATE KEY UPDATE `option_name` = VALUES(`option_name`), `option_value` = VALUES(`option_value`), `autoload` = VALUES(`autoload`)", $option, $serialized_value, $autoload ) );
					if ( ! $result )
						return false;
						
						if ( ! wp_installing() ) {
							if ( 'yes' == $autoload ) {
								$alloptions = wp_load_alloptions();
								$alloptions[ $option ] = $serialized_value;
								wp_cache_set( 'alloptions', $alloptions, 'options' );
							} else {
								wp_cache_set( $option, $serialized_value, 'options' );
							}
						}
						
						// This option exists now
						$notoptions = wp_cache_get( 'notoptions', 'options' ); // yes, again... we need it to be fresh
						if ( is_array( $notoptions ) && isset( $notoptions[$option] ) ) {
							unset( $notoptions[$option] );
							wp_cache_set( 'notoptions', $notoptions, 'options' );
						}
						
						/**
						 * Fires after a specific option has been added.
						 *
						 * The dynamic portion of the hook name, `$option`, refers to the option name.
						 *
						 * @since 2.5.0 As "add_option_{$name}"
						 * @since 3.0.0
						 *
						 * @param string $option Name of the option to add.
						 * @param mixed  $value  Value of the option.
						 */
						do_action( "add_option_{$option}", $option, $value );
						
						/**
						 * Fires after an option has been added.
						 *
						 * @since 2.9.0
						 *
						 * @param string $option Name of the added option.
						 * @param mixed  $value  Value of the option.
						 */
						do_action( 'added_option', $option, $value );
						return true;
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

function delete_option( $option ) {
global $wpdb;

$option = trim( $option );
if ( empty( $option ) )
	return false;
	
	wp_protect_special_option( $option );
	
	// Get the ID, if no ID then return
	$row = $wpdb->get_row( $wpdb->prepare( "SELECT autoload FROM $wpdb->options WHERE option_name = %s", $option ) );
	if ( is_null( $row ) )
		return false;
		
		/**
		 * Fires immediately before an option is deleted.
		 *
		 * @since 2.9.0
		 *
		 * @param string $option Name of the option to delete.
		 */
		do_action( 'delete_option', $option );
		
		$result = $wpdb->delete( $wpdb->options, array( 'option_name' => $option ) );
		if ( ! wp_installing() ) {
			if ( 'yes' == $row->autoload ) {
				$alloptions = wp_load_alloptions();
				if ( is_array( $alloptions ) && isset( $alloptions[$option] ) ) {
					unset( $alloptions[$option] );
					wp_cache_set( 'alloptions', $alloptions, 'options' );
				}
			} else {
				wp_cache_delete( $option, 'options' );
			}
		}
		if ( $result ) {
			
			/**
			 * Fires after a specific option has been deleted.
			 *
			 * The dynamic portion of the hook name, `$option`, refers to the option name.
			 *
			 * @since 3.0.0
			 *
			 * @param string $option Name of the deleted option.
			 */
			do_action( "delete_option_{$option}", $option );
			
			/**
			 * Fires after an option has been deleted.
			 *
			 * @since 2.9.0
			 *
			 * @param string $option Name of the deleted option.
			 */
			do_action( 'deleted_option', $option );
			return true;
		}
		return false;
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

function get_site_transient( $transient ) {
/**
 * Filters the value of an existing site transient.
 *
 * The dynamic portion of the hook name, `$transient`, refers to the transient name.
 *
 * Passing a truthy value to the filter will effectively short-circuit retrieval,
 * returning the passed value instead.
 *
 * @since 2.9.0
 * @since 4.4.0 The `$transient` parameter was added.
 *
 * @param mixed  $pre_site_transient The default value to return if the site transient does not exist.
 *                                   Any value other than false will short-circuit the retrieval
 *                                   of the transient, and return the returned value.
 * @param string $transient          Transient name.
 */
$pre = apply_filters( "pre_site_transient_{$transient}", false, $transient );

if ( false !== $pre )
	return $pre;
	
	if ( wp_using_ext_object_cache() ) {
		$value = wp_cache_get( $transient, 'site-transient' );
	} else {
		// Core transients that do not have a timeout. Listed here so querying timeouts can be avoided.
		$no_timeout = array('update_core', 'update_plugins', 'update_themes');
		$transient_option = '_site_transient_' . $transient;
		if ( ! in_array( $transient, $no_timeout ) ) {
			$transient_timeout = '_site_transient_timeout_' . $transient;
			$timeout = get_site_option( $transient_timeout );
			if ( false !== $timeout && $timeout < time() ) {
				delete_site_option( $transient_option  );
				delete_site_option( $transient_timeout );
				$value = false;
			}
		}
		
		if ( ! isset( $value ) )
			$value = get_site_option( $transient_option );
	}
	
	/**
	 * Filters the value of an existing site transient.
	 *
	 * The dynamic portion of the hook name, `$transient`, refers to the transient name.
	 *
	 * @since 2.9.0
	 * @since 4.4.0 The `$transient` parameter was added.
	 *
	 * @param mixed  $value     Value of site transient.
	 * @param string $transient Transient name.
	 */
	return apply_filters( "site_transient_{$transient}", $value, $transient );
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