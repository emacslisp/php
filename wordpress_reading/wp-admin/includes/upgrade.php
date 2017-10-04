<?php

require_once(ABSPATH . 'wp-admin/includes/admin.php');

function wp_check_mysql_version() {
	global $wpdb;
	$result = $wpdb->check_database_version ();
	if (is_wp_error ( $result ))
		die ( $result->get_error_message () );
}

function wp_cache_flush() {
	global $wp_object_cache;
	
	return $wp_object_cache->flush();
}

function make_db_current_silent( $tables = 'all' ) {
	dbDelta( $tables );
}

function wp_install( $blog_title, $user_name, $user_email, $public, $deprecated = '', $user_password = '', $language = '' ) {file_put_contents('/Users/ewu/output.log',print_r((new Exception)->getTraceAsString(),true). PHP_EOL . PHP_EOL,FILE_APPEND);
if ( !empty( $deprecated ) )
	_deprecated_argument( __FUNCTION__, '2.6.0' );
	
	wp_check_mysql_version();
	wp_cache_flush();
	make_db_current_silent();
	populate_options();
	populate_roles();
	
	update_option('blogname', $blog_title);
	update_option('admin_email', $user_email);
	update_option('blog_public', $public);
	
	// Freshness of site - in the future, this could get more specific about actions taken, perhaps.
	update_option( 'fresh_site', 1 );
	
	if ( $language ) {
		update_option( 'WPLANG', $language );
	}
	
	$guessurl = wp_guess_url();
	
	update_option('siteurl', $guessurl);
	
	// If not a public blog, don't ping.
	if ( ! $public )
		update_option('default_pingback_flag', 0);
		
		/*
		 * Create default user. If the user already exists, the user tables are
		 * being shared among sites. Just set the role in that case.
		 */
		$user_id = username_exists($user_name);
		$user_password = trim($user_password);
		$email_password = false;
		if ( !$user_id && empty($user_password) ) {
			$user_password = wp_generate_password( 12, false );
			$message = __('<strong><em>Note that password</em></strong> carefully! It is a <em>random</em> password that was generated just for you.');
			$user_id = wp_create_user($user_name, $user_password, $user_email);
			update_user_option($user_id, 'default_password_nag', true, true);
			$email_password = true;
		} elseif ( ! $user_id ) {
			// Password has been provided
			$message = '<em>'.__('Your chosen password.').'</em>';
			$user_id = wp_create_user($user_name, $user_password, $user_email);
		} else {
			$message = __('User already exists. Password inherited.');
		}
		
		$user = new WP_User($user_id);
		$user->set_role('administrator');
		
		wp_install_defaults($user_id);
		
		wp_install_maybe_enable_pretty_permalinks();
		
		flush_rewrite_rules();
		
		wp_new_blog_notification($blog_title, $guessurl, $user_id, ($email_password ? $user_password : __('The password you chose during the install.') ) );
		
		wp_cache_flush();
		
		/**
		 * Fires after a site is fully installed.
		 *
		 * @since 3.9.0
		 *
		 * @param WP_User $user The site owner.
		 */
		do_action( 'wp_install', $user );
		
		return array('url' => $guessurl, 'user_id' => $user_id, 'password' => $user_password, 'password_message' => $message);
}

?>