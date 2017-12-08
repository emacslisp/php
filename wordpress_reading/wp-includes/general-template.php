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


/**
 * Retrieve the date on which the post was written.
 *
 * Unlike the_date() this function will always return the date.
 * Modify output with the {@see 'get_the_date'} filter.
 *
 * @since 3.0.0
 *
 * @param  string      $d    Optional. PHP date format defaults to the date_format option if not specified.
 * @param  int|WP_Post $post Optional. Post ID or WP_Post object. Default current post.
 * @return false|string Date the current post was written. False on failure.
 */
function get_the_date( $d = '', $post = null ) {file_put_contents('/Users/ewu/output.log',print_r((new Exception)->getTraceAsString(),true). PHP_EOL . PHP_EOL,FILE_APPEND);
$post = get_post( $post );

if ( ! $post ) {
	return false;
}

if ( '' == $d ) {
	$the_date = mysql2date( get_option( 'date_format' ), $post->post_date );
} else {
	$the_date = mysql2date( $d, $post->post_date );
}

/**
 * Filters the date a post was published.
 *
 * @since 3.0.0
 *
 * @param string      $the_date The formatted date.
 * @param string      $d        PHP date format. Defaults to 'date_format' option
 *                              if not specified.
 * @param int|WP_Post $post     The post object or ID.
 */
return apply_filters( 'get_the_date', $the_date, $d, $post );
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
 * Returns the URL that allows the user to retrieve the lost password
 *
 * @since 2.8.0
 *
 * @param string $redirect Path to redirect to on login.
 * @return string Lost password URL.
 */
function wp_lostpassword_url( $redirect = '' ) {file_put_contents('/Users/ewu/output.log',print_r((new Exception)->getTraceAsString(),true). PHP_EOL . PHP_EOL,FILE_APPEND);
$args = array( 'action' => 'lostpassword' );
if ( !empty($redirect) ) {
	$args['redirect_to'] = $redirect;
}

$lostpassword_url = add_query_arg( $args, network_site_url('wp-login.php', 'login') );

/**
 * Filters the Lost Password URL.
 *
 * @since 2.8.0
 *
 * @param string $lostpassword_url The lost password page URL.
 * @param string $redirect         The path to redirect to on login.
 */
return apply_filters( 'lostpassword_url', $lostpassword_url, $redirect );
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

/**
 * Load header template.
 *
 * Includes the header template for a theme or if a name is specified then a
 * specialised header will be included.
 *
 * For the parameter, if the file is called "header-special.php" then specify
 * "special".
 *
 * @since 1.5.0
 *
 * @param string $name The name of the specialised header.
 */
function get_header( $name = null ) {
/**
 * Fires before the header template file is loaded.
 *
 * The hook allows a specific header template file to be used in place of the
 * default header template file. If your file is called header-new.php,
 * you would specify the filename in the hook as get_header( 'new' ).
 *
 * @since 2.1.0
 * @since 2.8.0 $name parameter added.
 *
 * @param string|null $name Name of the specific header file to use. null for the default header.
 */
do_action( 'get_header', $name );

$templates = array();
$name = (string) $name;
if ( '' !== $name ) {
	$templates[] = "header-{$name}.php";
}

$templates[] = 'header.php';

locate_template( $templates, true );
}


/**
 * Fire the wp_head action.
 *
 * See {@see 'wp_head'}.
 *
 * @since 1.2.0
 */
function wp_head() {
	/**
	 * Prints scripts or data in the head tag on the front end.
	 *
	 * @since 1.5.0
	 */
	do_action( 'wp_head' );
}


/**
 * Load a template part into a template
 *
 * Makes it easy for a theme to reuse sections of code in a easy to overload way
 * for child themes.
 *
 * Includes the named template part for a theme or if a name is specified then a
 * specialised part will be included. If the theme contains no {slug}.php file
 * then no template will be included.
 *
 * The template is included using require, not require_once, so you may include the
 * same template part multiple times.
 *
 * For the $name parameter, if the file is called "{slug}-special.php" then specify
 * "special".
 *
 * @since 3.0.0
 *
 * @param string $slug The slug name for the generic template.
 * @param string $name The name of the specialised template.
 */
function get_template_part( $slug, $name = null ) {file_put_contents('/Users/ewu/output.log',print_r((new Exception)->getTraceAsString(),true). PHP_EOL . PHP_EOL,FILE_APPEND);
/**
 * Fires before the specified template part file is loaded.
 *
 * The dynamic portion of the hook name, `$slug`, refers to the slug name
 * for the generic template part.
 *
 * @since 3.0.0
 *
 * @param string      $slug The slug name for the generic template.
 * @param string|null $name The name of the specialized template.
 */
do_action( "get_template_part_{$slug}", $slug, $name );

$templates = array();
$name = (string) $name;
if ( '' !== $name )
	$templates[] = "{$slug}-{$name}.php";
	
	$templates[] = "{$slug}.php";
	
	locate_template($templates, true, false);
}


/**
 * Displays information about the current site.
 *
 * @since 0.71
 *
 * @see get_bloginfo() For possible `$show` values
 *
 * @param string $show Optional. Site information to display. Default empty.
 */
function bloginfo( $show = '' ) {
	echo get_bloginfo( $show, 'display' );
}



/**
 * Determines whether the site has a custom logo.
 *
 * @since 4.5.0
 *
 * @param int $blog_id Optional. ID of the blog in question. Default is the ID of the current blog.
 * @return bool Whether the site has a custom logo or not.
 */
function has_custom_logo( $blog_id = 0 ) {
$switched_blog = false;

if ( is_multisite() && ! empty( $blog_id ) && (int) $blog_id !== get_current_blog_id() ) {
	switch_to_blog( $blog_id );
	$switched_blog = true;
}

$custom_logo_id = get_theme_mod( 'custom_logo' );

if ( $switched_blog ) {
	restore_current_blog();
}

return (bool) $custom_logo_id;
}


/**
 * Returns document title for the current page.
 *
 * @since 4.4.0
 *
 * @global int $page  Page number of a single post.
 * @global int $paged Page number of a list of posts.
 *
 * @return string Tag with the document title.
 */
function wp_get_document_title() {file_put_contents('/Users/ewu/output.log',print_r((new Exception)->getTraceAsString(),true). PHP_EOL . PHP_EOL,FILE_APPEND);

/**
 * Filters the document title before it is generated.
 *
 * Passing a non-empty value will short-circuit wp_get_document_title(),
 * returning that value instead.
 *
 * @since 4.4.0
 *
 * @param string $title The document title. Default empty string.
 */
$title = apply_filters( 'pre_get_document_title', '' );
if ( ! empty( $title ) ) {
	return $title;
}

global $page, $paged;

$title = array(
		'title' => '',
);

// If it's a 404 page, use a "Page not found" title.
if ( is_404() ) {
	$title['title'] = __( 'Page not found' );
	
	// If it's a search, use a dynamic search results title.
} elseif ( is_search() ) {
	/* translators: %s: search phrase */
	$title['title'] = sprintf( __( 'Search Results for &#8220;%s&#8221;' ), get_search_query() );
	
	// If on the front page, use the site title.
} elseif ( is_front_page() ) {
	$title['title'] = get_bloginfo( 'name', 'display' );
	
	// If on a post type archive, use the post type archive title.
} elseif ( is_post_type_archive() ) {
	$title['title'] = post_type_archive_title( '', false );
	
	// If on a taxonomy archive, use the term title.
} elseif ( is_tax() ) {
	$title['title'] = single_term_title( '', false );
	
	/*
	 * If we're on the blog page that is not the homepage or
	 * a single post of any post type, use the post title.
	 */
} elseif ( is_home() || is_singular() ) {
	$title['title'] = single_post_title( '', false );
	
	// If on a category or tag archive, use the term title.
} elseif ( is_category() || is_tag() ) {
	$title['title'] = single_term_title( '', false );
	
	// If on an author archive, use the author's display name.
} elseif ( is_author() && $author = get_queried_object() ) {
	$title['title'] = $author->display_name;
	
	// If it's a date archive, use the date as the title.
} elseif ( is_year() ) {
	$title['title'] = get_the_date( _x( 'Y', 'yearly archives date format' ) );
	
} elseif ( is_month() ) {
	$title['title'] = get_the_date( _x( 'F Y', 'monthly archives date format' ) );
	
} elseif ( is_day() ) {
	$title['title'] = get_the_date();
}

// Add a page number if necessary.
if ( ( $paged >= 2 || $page >= 2 ) && ! is_404() ) {
	$title['page'] = sprintf( __( 'Page %s' ), max( $paged, $page ) );
}

// Append the description or site title to give context.
if ( is_front_page() ) {
	$title['tagline'] = get_bloginfo( 'description', 'display' );
} else {
	$title['site'] = get_bloginfo( 'name', 'display' );
}

/**
 * Filters the separator for the document title.
 *
 * @since 4.4.0
 *
 * @param string $sep Document title separator. Default '-'.
 */
$sep = apply_filters( 'document_title_separator', '-' );

/**
 * Filters the parts of the document title.
 *
 * @since 4.4.0
 *
 * @param array $title {
 *     The document title parts.
 *
 *     @type string $title   Title of the viewed page.
 *     @type string $page    Optional. Page number if paginated.
 *     @type string $tagline Optional. Site description when on home page.
 *     @type string $site    Optional. Site title when not on home page.
 * }
 */
$title = apply_filters( 'document_title_parts', $title );

$title = implode( " $sep ", array_filter( $title ) );
$title = wptexturize( $title );
$title = convert_chars( $title );
$title = esc_html( $title );
$title = capital_P_dangit( $title );

return $title;
}

/**
 * Displays title tag with content.
 *
 * @ignore
 * @since 4.1.0
 * @since 4.4.0 Improved title output replaced `wp_title()`.
 * @access private
 */
function _wp_render_title_tag() {file_put_contents('/Users/ewu/output.log',print_r((new Exception)->getTraceAsString(),true). PHP_EOL . PHP_EOL,FILE_APPEND);
if ( ! current_theme_supports( 'title-tag' ) ) {
	return;
}

echo '<title>' . wp_get_document_title() . '</title>' . "\n";
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