<?php

class WP_Upgrader_Skin {
	
	public function __construct($args = array()) {
		$defaults = array( 'url' => '', 'nonce' => '', 'title' => '', 'context' => false );
		$this->options = wp_parse_args($args, $defaults);
	}
	
	public function set_upgrader(&$upgrader) {
		if ( is_object($upgrader) )
			$this->upgrader =& $upgrader;
			$this->add_strings();
	}
	
	public function add_strings() {
		
	}
	
	public function request_filesystem_credentials( $error = false, $context = '', $allow_relaxed_file_ownership = false ) {
		$url = $this->options['url'];
		if ( ! $context ) {
			$context = $this->options['context'];
		}
		
		if ( !empty($this->options['nonce']) ) {
			$url = wp_nonce_url($url, $this->options['nonce']);
		}
		
		$extra_fields = array();
		
		return request_filesystem_credentials( $url, '', $error, $context, $extra_fields, $allow_relaxed_file_ownership );
	}
}

?>