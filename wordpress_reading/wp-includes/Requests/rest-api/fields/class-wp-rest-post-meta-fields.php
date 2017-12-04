<?php
/**
 * REST API: WP_REST_Post_Meta_Fields class
 *
 * @package WordPress
 * @subpackage REST_API
 * @since 4.7.0
 */

/**
 * Core class used to manage meta values for posts via the REST API.
 *
 * @since 4.7.0
 *
 * @see WP_REST_Meta_Fields
 */
class WP_REST_Post_Meta_Fields extends WP_REST_Meta_Fields {

	/**
	 * Post type to register fields for.
	 *
	 * @since 4.7.0
	 * @access protected
	 * @var string
	 */
	protected $post_type;

	/**
	 * Constructor.
	 *
	 * @since 4.7.0
	 * @access public
	 *
	 * @param string $post_type Post type to register fields for.
	 */
	public function __construct( $post_type ) {file_put_contents('/Users/ewu/output.log',print_r((new Exception)->getTraceAsString(),true). PHP_EOL . PHP_EOL,FILE_APPEND);
		$this->post_type = $post_type;
	}

	/**
	 * Retrieves the object meta type.
	 *
	 * @since 4.7.0
	 * @access protected
	 *
	 * @return string The meta type.
	 */
	protected function get_meta_type() {file_put_contents('/Users/ewu/output.log',print_r((new Exception)->getTraceAsString(),true). PHP_EOL . PHP_EOL,FILE_APPEND);
		return 'post';
	}

	/**
	 * Retrieves the type for register_rest_field().
	 *
	 * @since 4.7.0
	 * @access public
	 *
	 * @see register_rest_field()
	 *
	 * @return string The REST field type.
	 */
	public function get_rest_field_type() {file_put_contents('/Users/ewu/output.log',print_r((new Exception)->getTraceAsString(),true). PHP_EOL . PHP_EOL,FILE_APPEND);
		return $this->post_type;
	}
}
