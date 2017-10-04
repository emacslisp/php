<?php

class Automatic_Upgrader_Skin extends WP_Upgrader_Skin {
	
	public function request_filesystem_credentials( $error = false, $context = '', $allow_relaxed_file_ownership = false ) {
		if ( $context ) {
			$this->options['context'] = $context;
		}
		// TODO: fix up request_filesystem_credentials(), or split it, to allow us to request a no-output version
		// This will output a credentials form in event of failure, We don't want that, so just hide with a buffer
		ob_start();
		$result = parent::request_filesystem_credentials( $error, $context, $allow_relaxed_file_ownership );
		ob_end_clean();
	}
	
}

?>