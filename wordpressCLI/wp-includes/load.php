<?php
/**
 * Attempt an early load of translations.
 *
 * Used for errors encountered during the initial loading process, before
 * the locale has been properly detected and loaded.
 *
 * Designed for unusual load sequences (like setup-config.php) or for when
 * the script will then terminate with an error, otherwise there is a risk
 * that a file can be double-included.
 *
 * @since 3.4.0
 * @access private
 *
 * @global WP_Locale $wp_locale The WordPress date and time locale object.
 *
 * @staticvar bool $loaded
 */
function wp_load_translations_early() {file_put_contents('/Users/ewu/output.log',print_r((new Exception)->getTraceAsString(),true). PHP_EOL . PHP_EOL,FILE_APPEND);
global $wp_locale;

static $loaded = false;
if ( $loaded )
	return;
	$loaded = true;
	
	if ( function_exists( 'did_action' ) && did_action( 'init' ) )
		return;
		
		// We need $wp_local_package
		require ABSPATH . WPINC . '/version.php';
		
		// Translation and localization
		require_once ABSPATH . WPINC . '/pomo/mo.php';
		require_once ABSPATH . WPINC . '/l10n.php';
		require_once ABSPATH . WPINC . '/class-wp-locale.php';
		require_once ABSPATH . WPINC . '/class-wp-locale-switcher.php';
		
		// General libraries
		require_once ABSPATH . WPINC . '/plugin.php';
		
		$locales = $locations = array();
		
		while ( true ) {
			if ( defined( 'WPLANG' ) ) {
				if ( '' == WPLANG )
					break;
					$locales[] = WPLANG;
			}
			
			if ( isset( $wp_local_package ) )
				$locales[] = $wp_local_package;
				
				if ( ! $locales )
					break;
					
					if ( defined( 'WP_LANG_DIR' ) && @is_dir( WP_LANG_DIR ) )
						$locations[] = WP_LANG_DIR;
						
						if ( defined( 'WP_CONTENT_DIR' ) && @is_dir( WP_CONTENT_DIR . '/languages' ) )
							$locations[] = WP_CONTENT_DIR . '/languages';
							
							if ( @is_dir( ABSPATH . 'wp-content/languages' ) )
								$locations[] = ABSPATH . 'wp-content/languages';
								
								if ( @is_dir( ABSPATH . WPINC . '/languages' ) )
									$locations[] = ABSPATH . WPINC . '/languages';
									
									if ( ! $locations )
										break;
										
										$locations = array_unique( $locations );
										
										foreach ( $locales as $locale ) {
											foreach ( $locations as $location ) {
												if ( file_exists( $location . '/' . $locale . '.mo' ) ) {
													load_textdomain( 'default', $location . '/' . $locale . '.mo' );
													if ( defined( 'WP_SETUP_CONFIG' ) && file_exists( $location . '/admin-' . $locale . '.mo' ) )
														load_textdomain( 'default', $location . '/admin-' . $locale . '.mo' );
														break 2;
												}
											}
										}
										
										break;
		}
		
		$wp_locale = new WP_Locale();
}
%>