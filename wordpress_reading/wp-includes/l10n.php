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


?>