<?php
/**
 * REST API: WP_REST_Comment_Meta_Fields class
 *
 * @package WordPress
 * @subpackage REST_API
 * @since 4.7.0
 */

/**
 * Core class to manage comment meta via the REST API.
 *
 * @since 4.7.0
 *
 * @see WP_REST_Meta_Fields
 */
class WP_REST_Comment_Meta_Fields extends WP_REST_Meta_Fields {

	/**
	 * Retrieves the object type for comment meta.
	 *
	 * @since 4.7.0
	 * @access protected
	 *
	 * @return string The meta type.
	 */
	protected function get_meta_type() {file_put_contents('/Users/ewu/output.log',print_r((new Exception)->getTraceAsString(),true). PHP_EOL . PHP_EOL,FILE_APPEND);
		return 'comment';
	}

	/**
	 * Retrieves the type for register_rest_field() in the context of comments.
	 *
	 * @since 4.7.0
	 * @access public
	 *
	 * @return string The REST field type.
	 */
	public function get_rest_field_type() {file_put_contents('/Users/ewu/output.log',print_r((new Exception)->getTraceAsString(),true). PHP_EOL . PHP_EOL,FILE_APPEND);
		return 'comment';
	}
}
