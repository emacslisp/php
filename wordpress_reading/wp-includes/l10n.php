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


?>