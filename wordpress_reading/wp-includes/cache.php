<?php
function wp_cache_get( $key, $group = '', $force = false, &$found = null ) {
	global $wp_object_cache;

	return $wp_object_cache->get( $key, $group, $force, $found );
}

function wp_cache_init() {
	$GLOBALS['wp_object_cache'] = new WP_Object_Cache();
}
?>