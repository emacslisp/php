<?php
// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'wordpress1234');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', '123456');

/** MySQL hostname */
define('DB_HOST', '127.0.0.1:3307');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

$table_prefix  = 'wp_';

echo $table_prefix;

echo DB_NAME;

global $wpdb;
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( dirname( __FILE__ ) ) . '/wordpressCLI/' );
}
define( 'WPINC', 'wp-includes' );

require_once( ABSPATH . WPINC . '/wp-db.php' );
if ( file_exists( WP_CONTENT_DIR . '/db.php' ) )
	require_once( WP_CONTENT_DIR . '/db.php' );
	
	if ( isset( $wpdb ) ) {
		return;
	}
	
	$wpdb = new wpdb( DB_USER, DB_PASSWORD, DB_NAME, DB_HOST );


?>