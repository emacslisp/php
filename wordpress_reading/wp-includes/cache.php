<?php

function wp_cache_add_non_persistent_groups( $groups ) {
// Default cache doesn't persist so nothing to do here.
}

function wp_cache_get( $key, $group = '', $force = false, &$found = null ) {
	global $wp_object_cache;

	return $wp_object_cache->get( $key, $group, $force, $found );
}

function wp_cache_set( $key, $data, $group = '', $expire = 0 ) {
	global $wp_object_cache;
	
	return $wp_object_cache->set( $key, $data, $group, (int) $expire );
}

function wp_cache_init() {
	$GLOBALS['wp_object_cache'] = new WP_Object_Cache();
}

class WP_Object_Cache {
	private $cache = array();
	public $cache_hits = 0;
	
	public $cache_misses = 0;
	
	protected $global_groups = array();
	private $multisite;
	
	public function flush() {
		$this->cache = array();
		
		return true;
	}
	
	protected function _exists( $key, $group ) {
		return isset( $this->cache[ $group ] ) && ( isset( $this->cache[ $group ][ $key ] ) || array_key_exists( $key, $this->cache[ $group ] ) );
	}
	
	public function set( $key, $data, $group = 'default', $expire = 0 ) {
		if ( empty( $group ) )
			$group = 'default';
		
		if ( $this->multisite && ! isset( $this->global_groups[ $group ] ) )
			$key = $this->blog_prefix . $key;
			
		if ( is_object( $data ) )
			$data = clone $data;
			
			$this->cache[$group][$key] = $data;
			return true;
	}
	
	public function get( $key, $group = 'default', $force = false, &$found = null ) {
		if ( empty( $group ) )
			$group = 'default';
			
			if ( $this->multisite && ! isset( $this->global_groups[ $group ] ) )
				$key = $this->blog_prefix . $key;
				
				if ( $this->_exists( $key, $group ) ) {
					$found = true;
					$this->cache_hits += 1;
					if ( is_object($this->cache[$group][$key]) )
						return clone $this->cache[$group][$key];
						else
							return $this->cache[$group][$key];
				}
				
				$found = false;
				$this->cache_misses += 1;
				return false;
	}
}
?>