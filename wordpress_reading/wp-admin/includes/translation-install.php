<?php

function wp_can_install_language_pack() {
	if (defined ( 'DISALLOW_FILE_MODS' ) && DISALLOW_FILE_MODS) {
		return false;
	}
	
	require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
	$skin = new Automatic_Upgrader_Skin ();
	$upgrader = new Language_Pack_Upgrader ( $skin );
	$upgrader->init ();
	
	$check = $upgrader->fs_connect ( array (
			WP_CONTENT_DIR,
			WP_LANG_DIR 
	) );
	
	if (! $check || is_wp_error ( $check )) {
		return false;
	}
	
	return true;
}

?>