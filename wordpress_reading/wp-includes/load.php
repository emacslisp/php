<?php

function require_wp_db() {
	//file_put_contents ( '/Users/ewu/output.log', print_r ( (new Exception ())->getTraceAsString (), true ) . PHP_EOL . PHP_EOL, FILE_APPEND );
	global $wpdb;
	
	require_once (ABSPATH . WPINC . '/wp-db.php');
	if (file_exists ( WP_CONTENT_DIR . '/db.php' ))
		require_once (WP_CONTENT_DIR . '/db.php');
	
	if (isset ( $wpdb )) {
		return;
	}
	
	$wpdb = new wpdb ( DB_USER, DB_PASSWORD, DB_NAME, DB_HOST );
}

?>