<?php
// file name l10n.php
function _e($text, $domain = 'default') {
	echo $text;
}

function __($text, $domain = 'default') {
	return $text;
}

function _x($text) {
	return $text;
}

function esc_attr_e( $text, $domain = 'default' ) {
	return $text;
}

function is_rtl() {
	global $wp_locale;
	if ( ! ( $wp_locale instanceof WP_Locale ) ) {
		return false;
	}
	return $wp_locale->is_rtl();
}

function get_locale() {
	global $locale, $wp_local_package;
	
	if ( isset( $locale ) ) {
		return apply_filters( 'locale', $locale );
	}
	
	if ( isset( $wp_local_package ) ) {
		$locale = $wp_local_package;
	}
	
	// WPLANG was defined in wp-config.
	if ( defined( 'WPLANG' ) ) {
		$locale = WPLANG;
	}
	
	// If multisite, check options.
	if ( is_multisite() ) {
		// Don't check blog option when installing.
		if ( wp_installing() || ( false === $ms_locale = get_option( 'WPLANG' ) ) ) {
			$ms_locale = get_site_option( 'WPLANG' );
		}
		
		if ( $ms_locale !== false ) {
			$locale = $ms_locale;
		}
	} else {
		$db_locale = get_option( 'WPLANG' );
		if ( $db_locale !== false ) {
			$locale = $db_locale;
		}
	}
	
	if ( empty( $locale ) ) {
		$locale = 'en_US';
	}

	return apply_filters( 'locale', $locale );
}


function unload_textdomain( $domain ) {
	global $l10n, $l10n_unloaded;
	
	$l10n_unloaded = (array) $l10n_unloaded;
	
	/**
	 * Filters whether to override the text domain unloading.
	 *
	 * @since 3.0.0
	 *
	 * @param bool   $override Whether to override the text domain unloading. Default false.
	 * @param string $domain   Text domain. Unique identifier for retrieving translated strings.
	 */
	$plugin_override = apply_filters( 'override_unload_textdomain', false, $domain );
	
	if ( $plugin_override ) {
		$l10n_unloaded[ $domain ] = true;
		
		return true;
	}
	
	/**
	 * Fires before the text domain is unloaded.
	 *
	 * @since 3.0.0
	 *
	 * @param string $domain Text domain. Unique identifier for retrieving translated strings.
	 */
	do_action( 'unload_textdomain', $domain );
	
	if ( isset( $l10n[$domain] ) ) {
		unset( $l10n[$domain] );
		
		$l10n_unloaded[ $domain ] = true;
		
		return true;
	}
	
	return false;
}

function get_available_languages( $dir = null ) {
	$languages = array();
	
	$lang_files = glob( ( is_null( $dir ) ? WP_LANG_DIR : $dir ) . '/*.mo' );
	if ( $lang_files ) {
		foreach ( $lang_files as $lang_file ) {
			$lang_file = basename( $lang_file, '.mo' );
			if ( 0 !== strpos( $lang_file, 'continents-cities' ) && 0 !== strpos( $lang_file, 'ms-' ) &&
					0 !== strpos( $lang_file, 'admin-' ) ) {
						$languages[] = $lang_file;
					}
		}
	}
	
	/**
	 * Filters the list of available language codes.
	 *
	 * @since 4.7.0
	 *
	 * @param array  $languages An array of available language codes.
	 * @param string $dir       The directory where the language files were found.
	 */
	return apply_filters( 'get_available_languages', $languages, $dir );
}


function load_default_textdomain( $locale = null ) {
	if ( null === $locale ) {
		$locale = is_admin() ? get_user_locale() : get_locale();
	}
	
	// Unload previously loaded strings so we can switch translations.
	unload_textdomain( 'default' );
	
	$return = load_textdomain( 'default', WP_LANG_DIR . "/$locale.mo" );
	
	if ( ( is_multisite() || ( defined( 'WP_INSTALLING_NETWORK' ) && WP_INSTALLING_NETWORK ) ) && ! file_exists(  WP_LANG_DIR . "/admin-$locale.mo" ) ) {
		load_textdomain( 'default', WP_LANG_DIR . "/ms-$locale.mo" );
		return $return;
	}
	
	if ( is_admin() || wp_installing() || ( defined( 'WP_REPAIRING' ) && WP_REPAIRING ) ) {
		load_textdomain( 'default', WP_LANG_DIR . "/admin-$locale.mo" );
	}
	
	if ( is_network_admin() || ( defined( 'WP_INSTALLING_NETWORK' ) && WP_INSTALLING_NETWORK ) )
		load_textdomain( 'default', WP_LANG_DIR . "/admin-network-$locale.mo" );
		
		return $return;
}

function load_textdomain( $domain, $mofile ) {
	global $l10n, $l10n_unloaded;
	
	$l10n_unloaded = (array) $l10n_unloaded;
	
	/**
	 * Filters whether to override the .mo file loading.
	 *
	 * @since 2.9.0
	 *
	 * @param bool   $override Whether to override the .mo file loading. Default false.
	 * @param string $domain   Text domain. Unique identifier for retrieving translated strings.
	 * @param string $mofile   Path to the MO file.
	 */
	$plugin_override = apply_filters( 'override_load_textdomain', false, $domain, $mofile );
	
	if ( true == $plugin_override ) {
		unset( $l10n_unloaded[ $domain ] );
		
		return true;
	}
	
	/**
	 * Fires before the MO translation file is loaded.
	 *
	 * @since 2.9.0
	 *
	 * @param string $domain Text domain. Unique identifier for retrieving translated strings.
	 * @param string $mofile Path to the .mo file.
	 */
	do_action( 'load_textdomain', $domain, $mofile );
	
	/**
	 * Filters MO file path for loading translations for a specific text domain.
	 *
	 * @since 2.9.0
	 *
	 * @param string $mofile Path to the MO file.
	 * @param string $domain Text domain. Unique identifier for retrieving translated strings.
	 */
	$mofile = apply_filters( 'load_textdomain_mofile', $mofile, $domain );
	
	if ( !is_readable( $mofile ) ) return false;
	
	$mo = new MO();
	if ( !$mo->import_from_file( $mofile ) ) return false;
	
	if ( isset( $l10n[$domain] ) )
		$mo->merge_with( $l10n[$domain] );
		
		unset( $l10n_unloaded[ $domain ] );
		
		$l10n[$domain] = &$mo;
		
		return true;
}


?>