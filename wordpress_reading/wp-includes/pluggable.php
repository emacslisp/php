<?php
if (! function_exists ( 'wp_generate_password' )) :
	/**
	 * Generates a random password drawn from the defined set of characters.
	 *
	 * @since 2.5.0
	 *       
	 * @param int $length
	 *        	Optional. The length of password to generate. Default 12.
	 * @param bool $special_chars
	 *        	Optional. Whether to include standard special characters.
	 *        	Default true.
	 * @param bool $extra_special_chars
	 *        	Optional. Whether to include other special characters.
	 *        	Used when generating secret keys and salts. Default false.
	 * @return string The random password.
	 */
	function wp_generate_password($length = 12, $special_chars = true, $extra_special_chars = false) {
		$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		if ($special_chars)
			$chars .= '!@#$%^&*()';
		if ($extra_special_chars)
			$chars .= '-_ []{}<>~`+=,.;:/?|';
		
		$password = '';
		for($i = 0; $i < $length; $i ++) {
			$password .= substr ( $chars, wp_rand ( 0, strlen ( $chars ) - 1 ), 1 );
		}
		
		/**
		 * Filters the randomly-generated password.
		 *
		 * @since 3.0.0
		 *       
		 * @param string $password
		 *        	The generated password.
		 */
		return apply_filters ( 'random_password', $password );
	}
endif;

if (! function_exists ( 'wp_rand' )) :
	/**
	 * Generates a random number
	 *
	 * @since 2.6.2
	 * @since 4.4.0 Uses PHP7 random_int() or the random_compat library if available.
	 *       
	 * @global string $rnd_value
	 * @staticvar string $seed
	 * @staticvar bool $external_rand_source_available
	 *           
	 * @param int $min
	 *        	Lower limit for the generated number
	 * @param int $max
	 *        	Upper limit for the generated number
	 * @return int A random number between min and max
	 */
	function wp_rand($min = 0, $max = 0) {
		global $rnd_value;
		
		// Some misconfigured 32bit environments (Entropy PHP, for example) truncate integers larger than PHP_INT_MAX to PHP_INT_MAX rather than overflowing them to floats.
		$max_random_number = 3000000000 === 2147483647 ? ( float ) "4294967295" : 4294967295; // 4294967295 = 0xffffffff
		                                                                                    
		// We only handle Ints, floats are truncated to their integer value.
		$min = ( int ) $min;
		$max = ( int ) $max;
		
		// Use PHP's CSPRNG, or a compatible method
		static $use_random_int_functionality = true;
		if ($use_random_int_functionality) {
			try {
				$_max = (0 != $max) ? $max : $max_random_number;
				// wp_rand() can accept arguments in either order, PHP cannot.
				$_max = max ( $min, $_max );
				$_min = min ( $min, $_max );
				// @note: random_int is for php 7 only
				$val = rand ( $_min, $_max );
				if (false !== $val) {
					return absint ( $val );
				} else {
					$use_random_int_functionality = false;
				}
			} catch ( Error $e ) {
				$use_random_int_functionality = false;
			} catch ( Exception $e ) {
				$use_random_int_functionality = false;
			}
		}
		
		// Reset $rnd_value after 14 uses
		// 32(md5) + 40(sha1) + 40(sha1) / 8 = 14 random numbers from $rnd_value
		if (strlen ( $rnd_value ) < 8) {
			if (defined ( 'WP_SETUP_CONFIG' ))
				static $seed = '';
			else
				$seed = get_transient ( 'random_seed' );
			$rnd_value = md5 ( uniqid ( microtime () . mt_rand (), true ) . $seed );
			$rnd_value .= sha1 ( $rnd_value );
			$rnd_value .= sha1 ( $rnd_value . $seed );
			$seed = md5 ( $seed . $rnd_value );
			if (! defined ( 'WP_SETUP_CONFIG' ) && ! defined ( 'WP_INSTALLING' )) {
				set_transient ( 'random_seed', $seed );
			}
		}
		
		// Take the first 8 digits for our value
		$value = substr ( $rnd_value, 0, 8 );
		
		// Strip the first eight, leaving the remainder for the next call to wp_rand().
		$rnd_value = substr ( $rnd_value, 8 );
		
		$value = abs ( hexdec ( $value ) );
		
		// Reduce the value to be within the min - max range
		if ($max != 0)
			$value = $min + ($max - $min + 1) * $value / ($max_random_number + 1);
		
		return abs ( intval ( $value ) );
	}
endif;

if (! function_exists ( 'get_userdata' )) :
	/**
	 * Retrieve user info by user ID.
	 *
	 * @since 0.71
	 *       
	 * @param int $user_id
	 *        	User ID
	 * @return WP_User|false WP_User object on success, false on failure.
	 */
	function get_userdata($user_id) {
		return get_user_by ( 'id', $user_id );
	}
endif;

if (! function_exists ( 'get_user_by' )) :
	/**
	 * Retrieve user info by a given field
	 *
	 * @since 2.8.0
	 * @since 4.4.0 Added 'ID' as an alias of 'id' for the `$field` parameter.
	 *       
	 * @param string $field
	 *        	The field to retrieve the user with. id | ID | slug | email | login.
	 * @param int|string $value
	 *        	A value for $field. A user ID, slug, email address, or login name.
	 * @return WP_User|false WP_User object on success, false on failure.
	 */
	function get_user_by($field, $value) {
		$userdata = WP_User::get_data_by ( $field, $value );
		
		if (! $userdata)
			return false;
		
		$user = new WP_User ();
		$user->init ( $userdata );
		
		return $user;
	}
endif;

?>