<?php

class WP_User {
	public function __construct( $id = 0, $name = '', $blog_id = '' ) {
	if ( ! isset( self::$back_compat_keys ) ) {
		$prefix = $GLOBALS['wpdb']->prefix;
		self::$back_compat_keys = array(
				'user_firstname' => 'first_name',
				'user_lastname' => 'last_name',
				'user_description' => 'description',
				'user_level' => $prefix . 'user_level',
				$prefix . 'usersettings' => $prefix . 'user-settings',
				$prefix . 'usersettingstime' => $prefix . 'user-settings-time',
		);
	}
	
	if ( $id instanceof WP_User ) {
		$this->init( $id->data, $blog_id );
		return;
	} elseif ( is_object( $id ) ) {
		$this->init( $id, $blog_id );
		return;
	}
	
	if ( ! empty( $id ) && ! is_numeric( $id ) ) {
		$name = $id;
		$id = 0;
	}
	
	if ( $id ) {
		$data = self::get_data_by( 'id', $id );
	} else {
		$data = self::get_data_by( 'login', $name );
	}
	
	if ( $data ) {
		$this->init( $data, $blog_id );
	} else {
		$this->data = new stdClass;
	}
	}
}

?>