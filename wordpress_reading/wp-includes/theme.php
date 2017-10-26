<?php
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