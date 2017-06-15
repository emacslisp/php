
<?php

define('WP_INSTALLING', true);

define('WP_SETUP_CONFIG', true);

function _e($text, $domain='default') {
	echo $text;
}

?>

<h1 class="screen-reader-text"><?php _e( 'Before getting started' ) ?></h1>
<p><?php _e( 'Welcome to WordPress. Before getting started, we need some information on the database. You will need to know the following items before proceeding.' ) ?></p>
<ol>
	<li><?php _e( 'Database name' ); ?></li>
	<li><?php _e( 'Database username' ); ?></li>
	<li><?php _e( 'Database password' ); ?></li>
	<li><?php _e( 'Database host' ); ?></li>
	<li><?php _e( 'Table prefix (if you want to run more than one WordPress in a single database)' ); ?></li>
</ol>
