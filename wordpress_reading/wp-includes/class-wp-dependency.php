<?php

class _WP_Dependency {
	public $handle;
	public $src;
	public $deps = array();

	public $ver = false;

	public $args = null;  // Custom property, such as $in_footer or $media.

	public $extra = array();

	public function __construct() {file_put_contents('/Users/ewu/output2.log',print_r((new Exception)->getTraceAsString(),true). PHP_EOL . PHP_EOL,FILE_APPEND);
		@list( $this->handle, $this->src, $this->deps, $this->ver, $this->args ) = func_get_args();
		if ( ! is_array($this->deps) )
			$this->deps = array();
	}

	public function add_data( $name, $data ) {file_put_contents('/Users/ewu/output2.log',print_r((new Exception)->getTraceAsString(),true). PHP_EOL . PHP_EOL,FILE_APPEND);
		if ( !is_scalar($name) )
			return false;
		$this->extra[$name] = $data;
		return true;
	}

}

?>
