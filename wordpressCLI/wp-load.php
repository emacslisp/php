<?php

define('WP_DEBUG', false);

if ( !defined('ABSPATH') )
    define('ABSPATH', dirname(__FILE__) . '/');

define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );

define( 'WPINC', 'wp-includes' );

require_once ABSPATH . WPINC . '/plugin.php';

require_once( ABSPATH . WPINC . '/load.php' );

?>