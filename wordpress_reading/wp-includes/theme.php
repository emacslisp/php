<?php
function get_stylesheet() {
/**
 * Filters the name of current stylesheet.
 *
 * @since 1.5.0
 *
 * @param string $stylesheet Name of the current stylesheet.
 */
return apply_filters( 'stylesheet', get_option( 'stylesheet' ) );
}

function get_stylesheet_directory() {
$stylesheet = get_stylesheet();
$theme_root = get_theme_root( $stylesheet );
$stylesheet_dir = "$theme_root/$stylesheet";

/**
 * Filters the stylesheet directory path for current theme.
 *
 * @since 1.5.0
 *
 * @param string $stylesheet_dir Absolute path to the current theme.
 * @param string $stylesheet     Directory name of the current theme.
 * @param string $theme_root     Absolute path to themes directory.
 */
return apply_filters( 'stylesheet_directory', $stylesheet_dir, $stylesheet, $theme_root );
}

function get_template() {
	/**
	 * Filters the name of the current theme.
	 *
	 * @since 1.5.0
	 *       
	 * @param string $template
	 *        	Current theme's directory name.
	 */
	return apply_filters ( 'template', get_option ( 'template' ) );
}

function get_template_directory() {
	$template = get_template ();
	$theme_root = get_theme_root ( $template );
	$template_dir = "$theme_root/$template";
	
	/**
	 * Filters the current theme directory path.
	 *
	 * @since 1.5.0
	 *       
	 * @param string $template_dir
	 *        	The URI of the current theme directory.
	 * @param string $template
	 *        	Directory name of the current theme.
	 * @param string $theme_root
	 *        	Absolute path to the themes directory.
	 */
	return apply_filters ( 'template_directory', $template_dir, $template, $theme_root );
}

function get_theme_roots() {
	global $wp_theme_directories;
	
	if (count ( $wp_theme_directories ) <= 1)
		return '/themes';
	
	$theme_roots = get_site_transient ( 'theme_roots' );
	if (false === $theme_roots) {
		search_theme_directories ( true ); // Regenerate the transient.
		$theme_roots = get_site_transient ( 'theme_roots' );
	}
	return $theme_roots;
}

function search_theme_directories($force = false) {
	global $wp_theme_directories;
	static $found_themes = null;
	
	if (empty ( $wp_theme_directories ))
		return false;
	
	if (! $force && isset ( $found_themes ))
		return $found_themes;
	
	$found_themes = array ();
	
	$wp_theme_directories = ( array ) $wp_theme_directories;
	$relative_theme_roots = array ();
	
	// Set up maybe-relative, maybe-absolute array of theme directories.
	// We always want to return absolute, but we need to cache relative
	// to use in get_theme_root().
	foreach ( $wp_theme_directories as $theme_root ) {
		if (0 === strpos ( $theme_root, WP_CONTENT_DIR ))
			$relative_theme_roots [str_replace ( WP_CONTENT_DIR, '', $theme_root )] = $theme_root;
		else
			$relative_theme_roots [$theme_root] = $theme_root;
	}
	
	/**
	 * Filters whether to get the cache of the registered theme directories.
	 *
	 * @since 3.4.0
	 *       
	 * @param bool $cache_expiration
	 *        	Whether to get the cache of the theme directories. Default false.
	 * @param string $cache_directory
	 *        	Directory to be searched for the cache.
	 */
	if ($cache_expiration = apply_filters ( 'wp_cache_themes_persistently', false, 'search_theme_directories' )) {
		$cached_roots = get_site_transient ( 'theme_roots' );
		if (is_array ( $cached_roots )) {
			foreach ( $cached_roots as $theme_dir => $theme_root ) {
				// A cached theme root is no longer around, so skip it.
				if (! isset ( $relative_theme_roots [$theme_root] ))
					continue;
				$found_themes [$theme_dir] = array (
						'theme_file' => $theme_dir . '/style.css',
						'theme_root' => $relative_theme_roots [$theme_root]  // Convert relative to absolute.
				);
			}
			return $found_themes;
		}
		if (! is_int ( $cache_expiration ))
			$cache_expiration = 1800; // half hour
	} else {
		$cache_expiration = 1800; // half hour
	}
	
	/* Loop the registered theme directories and extract all themes */
	foreach ( $wp_theme_directories as $theme_root ) {
		
		// Start with directories in the root of the current theme directory.
		$dirs = @ scandir ( $theme_root );
		if (! $dirs) {
			trigger_error ( "$theme_root is not readable", E_USER_NOTICE );
			continue;
		}
		foreach ( $dirs as $dir ) {
			if (! is_dir ( $theme_root . '/' . $dir ) || $dir [0] == '.' || $dir == 'CVS')
				continue;
			if (file_exists ( $theme_root . '/' . $dir . '/style.css' )) {
				// wp-content/themes/a-single-theme
				// wp-content/themes is $theme_root, a-single-theme is $dir
				$found_themes [$dir] = array (
						'theme_file' => $dir . '/style.css',
						'theme_root' => $theme_root 
				);
			} else {
				$found_theme = false;
				// wp-content/themes/a-folder-of-themes/*
				// wp-content/themes is $theme_root, a-folder-of-themes is $dir, then themes are $sub_dirs
				$sub_dirs = @ scandir ( $theme_root . '/' . $dir );
				if (! $sub_dirs) {
					trigger_error ( "$theme_root/$dir is not readable", E_USER_NOTICE );
					continue;
				}
				foreach ( $sub_dirs as $sub_dir ) {
					if (! is_dir ( $theme_root . '/' . $dir . '/' . $sub_dir ) || $dir [0] == '.' || $dir == 'CVS')
						continue;
					if (! file_exists ( $theme_root . '/' . $dir . '/' . $sub_dir . '/style.css' ))
						continue;
					$found_themes [$dir . '/' . $sub_dir] = array (
							'theme_file' => $dir . '/' . $sub_dir . '/style.css',
							'theme_root' => $theme_root 
					);
					$found_theme = true;
				}
				// Never mind the above, it's just a theme missing a style.css.
				// Return it; WP_Theme will catch the error.
				if (! $found_theme)
					$found_themes [$dir] = array (
							'theme_file' => $dir . '/style.css',
							'theme_root' => $theme_root 
					);
			}
		}
	}
	
	asort ( $found_themes );
	
	$theme_roots = array ();
	$relative_theme_roots = array_flip ( $relative_theme_roots );
	
	foreach ( $found_themes as $theme_dir => $theme_data ) {
		$theme_roots [$theme_dir] = $relative_theme_roots [$theme_data ['theme_root']]; // Convert absolute to relative.
	}
	
	if ($theme_roots != get_site_transient ( 'theme_roots' ))
		set_site_transient ( 'theme_roots', $theme_roots, $cache_expiration );
	
	return $found_themes;
}

function get_raw_theme_root($stylesheet_or_template, $skip_cache = false) {
	global $wp_theme_directories;
	
	if (count ( $wp_theme_directories ) <= 1)
		return '/themes';
	
	$theme_root = false;
	
	// If requesting the root for the current theme, consult options to avoid calling get_theme_roots()
	if (! $skip_cache) {
		if (get_option ( 'stylesheet' ) == $stylesheet_or_template)
			$theme_root = get_option ( 'stylesheet_root' );
		elseif (get_option ( 'template' ) == $stylesheet_or_template)
			$theme_root = get_option ( 'template_root' );
	}
	
	if (empty ( $theme_root )) {
		$theme_roots = get_theme_roots ();
		if (! empty ( $theme_roots [$stylesheet_or_template] ))
			$theme_root = $theme_roots [$stylesheet_or_template];
	}
	
	return $theme_root;
}


function get_theme_root($stylesheet_or_template = false) {
	global $wp_theme_directories;
	
	if ($stylesheet_or_template && $theme_root = get_raw_theme_root ( $stylesheet_or_template )) {
		// Always prepend WP_CONTENT_DIR unless the root currently registered as a theme directory.
		// This gives relative theme roots the benefit of the doubt when things go haywire.
		if (! in_array ( $theme_root, ( array ) $wp_theme_directories ))
			$theme_root = WP_CONTENT_DIR . $theme_root;
	} else {
		$theme_root = WP_CONTENT_DIR . '/themes';
	}
	
	/**
	 * Filters the absolute path to the themes directory.
	 *
	 * @since 1.5.0
	 *       
	 * @param string $theme_root
	 *        	Absolute path to themes directory.
	 */
	return apply_filters ( 'theme_root', $theme_root );
}

function register_theme_directory($directory) {
	global $wp_theme_directories;
	
	if (! file_exists ( $directory )) {
		// Try prepending as the theme directory could be relative to the content directory
		$directory = WP_CONTENT_DIR . '/' . $directory;
		// If this directory does not exist, return and do not register
		if (! file_exists ( $directory )) {
			return false;
		}
	}
	
	if (! is_array ( $wp_theme_directories )) {
		$wp_theme_directories = array ();
	}
	
	$untrailed = untrailingslashit ( $directory );
	if (! empty ( $untrailed ) && ! in_array ( $untrailed, $wp_theme_directories )) {
		$wp_theme_directories [] = $untrailed;
	}
	
	return true;
}

?>