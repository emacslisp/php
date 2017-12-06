<?php

function wp_admin_css( $file = 'wp-admin', $force_echo = false ) {
	// For backward compatibility
	/*$handle = 0 === strpos( $file, 'css/' ) ? substr( $file, 4 ) : $file;
	
	if ( wp_styles()->query( $handle ) ) {
		if ( $force_echo || did_action( 'wp_print_styles' ) ) // we already printed the style queue. Print this one immediately
			wp_print_styles( $handle );
			else // Add to style queue
				wp_enqueue_style( $handle );
				return;
	}*/
	
	/**
	 * Filters the stylesheet link to the specified CSS file.
	 *
	 * If the site is set to display right-to-left, the RTL stylesheet link
	 * will be used instead.
	 *
	 * @since 2.3.0
	 *
	 * @param string $file Style handle name or filename (without ".css" extension)
	 *                     relative to wp-admin/. Defaults to 'wp-admin'.
	 */
	echo apply_filters( 'wp_admin_css', "<link rel='stylesheet' href='" . esc_url( wp_admin_css_uri( $file ) ) . "' type='text/css' />\n", $file );
	
	/*
	 if ( function_exists( 'is_rtl' ) && is_rtl() ) {
		
		echo apply_filters( 'wp_admin_css', "<link rel='stylesheet' href='" . esc_url( wp_admin_css_uri( "$file-rtl" ) ) . "' type='text/css' />\n", "$file-rtl" );
	}
	*/

}

function checked( $checked, $current = true, $echo = true ) {
	return __checked_selected_helper( $checked, $current, $echo, 'checked' );
}

function __checked_selected_helper( $helper, $current, $echo, $type ) {
if ( (string) $helper === (string) $current )
	$result = " $type='$type'";
	else
		$result = '';
		
		if ( $echo )
			echo $result;
			
			return $result;
}

function language_attributes( $doctype = 'html' ) {
	echo get_language_attributes( $doctype );
}

/**
 * Retrieves the login URL.
 *
 * @since 2.7.0
 *
 * @param string $redirect     Path to redirect to on log in.
 * @param bool   $force_reauth Whether to force reauthorization, even if a cookie is present.
 *                             Default false.
 * @return string The login URL. Not HTML-encoded.
 */
function wp_login_url($redirect = '', $force_reauth = false) {
	$login_url = site_url('wp-login.php', 'login');
	
	if ( !empty($redirect) )
		$login_url = add_query_arg('redirect_to', urlencode($redirect), $login_url);
		
		if ( $force_reauth )
			$login_url = add_query_arg('reauth', '1', $login_url);
			
			/**
			 * Filters the login URL.
			 *
			 * @since 2.8.0
			 * @since 4.2.0 The `$force_reauth` parameter was added.
			 *
			 * @param string $login_url    The login URL. Not HTML-encoded.
			 * @param string $redirect     The path to redirect to on login, if supplied.
			 * @param bool   $force_reauth Whether to force reauthorization, even if a cookie is present.
			 */
			return apply_filters( 'login_url', $login_url, $redirect, $force_reauth );
}

function get_language_attributes( $doctype = 'html' ) {
	$attributes = array();
	
	if ( function_exists( 'is_rtl' ) && is_rtl() )
		$attributes[] = 'dir="rtl"';
		
		if ( $lang = get_bloginfo('language') ) {
			if ( get_option('html_type') == 'text/html' || $doctype == 'html' )
				$attributes[] = "lang=\"$lang\"";
				
				if ( get_option('html_type') != 'text/html' || $doctype == 'xhtml' )
					$attributes[] = "xml:lang=\"$lang\"";
		}
		
	$output = implode(' ', $attributes);
		
	return apply_filters( 'language_attributes', $output, $doctype );
}

function get_bloginfo( $show = '', $filter = 'raw' ) {
switch( $show ) {
	case 'home' : // DEPRECATED
	case 'siteurl' : // DEPRECATED
		_deprecated_argument( __FUNCTION__, '2.2.0', sprintf(
		/* translators: 1: 'siteurl'/'home' argument, 2: bloginfo() function name, 3: 'url' argument */
		__( 'The %1$s option is deprecated for the family of %2$s functions. Use the %3$s option instead.' ),
		'<code>' . $show . '</code>',
		'<code>bloginfo()</code>',
		'<code>url</code>'
				) );
	case 'url' :
		$output = home_url();
		break;
	case 'wpurl' :
		$output = site_url();
		break;
	case 'description':
		$output = get_option('blogdescription');
		break;
	case 'rdf_url':
		$output = get_feed_link('rdf');
		break;
	case 'rss_url':
		$output = get_feed_link('rss');
		break;
	case 'rss2_url':
		$output = get_feed_link('rss2');
		break;
	case 'atom_url':
		$output = get_feed_link('atom');
		break;
	case 'comments_atom_url':
		$output = get_feed_link('comments_atom');
		break;
	case 'comments_rss2_url':
		$output = get_feed_link('comments_rss2');
		break;
	case 'pingback_url':
		$output = site_url( 'xmlrpc.php' );
		break;
	case 'stylesheet_url':
		$output = get_stylesheet_uri();
		break;
	case 'stylesheet_directory':
		$output = get_stylesheet_directory_uri();
		break;
	case 'template_directory':
	case 'template_url':
		$output = get_template_directory_uri();
		break;
	case 'admin_email':
		$output = get_option('admin_email');
		break;
	case 'charset':
		$output = get_option('blog_charset');
		if ('' == $output) $output = 'UTF-8';
		break;
	case 'html_type' :
		$output = get_option('html_type');
		break;
	case 'version':
		global $wp_version;
		$output = $wp_version;
		break;
	case 'language':

		$output = __( 'html_lang_attribute' );
		if ( 'html_lang_attribute' === $output || preg_match( '/[^a-zA-Z0-9-]/', $output ) ) {
			$output = get_locale();
			$output = str_replace( '_', '-', $output );
		}
		break;
	case 'text_direction':
		_deprecated_argument( __FUNCTION__, '2.2.0', sprintf(
		/* translators: 1: 'text_direction' argument, 2: bloginfo() function name, 3: is_rtl() function name */
		__( 'The %1$s option is deprecated for the family of %2$s functions. Use the %3$s function instead.' ),
		'<code>' . $show . '</code>',
		'<code>bloginfo()</code>',
		'<code>is_rtl()</code>'
				) );
		if ( function_exists( 'is_rtl' ) ) {
		$output = is_rtl() ? 'rtl' : 'ltr';
		} else {
			$output = 'ltr';
		}
		break;
	case 'name':
	default:
		$output = get_option('blogname');
		break;
}

$url = true;
if (strpos($show, 'url') === false &&
		strpos($show, 'directory') === false &&
		strpos($show, 'home') === false)
	$url = false;
	
	if ( 'display' == $filter ) {
		if ( $url ) {

			$output = apply_filters( 'bloginfo_url', $output, $show );
		} else {

			$output = apply_filters( 'bloginfo', $output, $show );
		}
	}
	
	return $output;
}


?>