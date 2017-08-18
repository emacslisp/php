<?php
define ( 'WP_INSTALLING', true );

define ( 'WP_SETUP_CONFIG', true );

define( 'WPINC', 'wp-includes' );

if ( !defined('WP_DEBUG_DISPLAY') )
	define( 'WP_DEBUG_DISPLAY', true );

if ( !defined('WP_DEBUG') )
		define( 'WP_DEBUG', true );

function is_rtl() {
	return false;
}

function _e($text, $domain = 'default') {
	echo $text;
}

function __($text, $domain = 'default') {
	return $text;
}

function _x($text) {
	return $text;
}

function wp_die($message) {
	die ( $message );
}

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( dirname( __FILE__ ) ) . '/' );
}
// Support wp-config-sample.php one level up, for the develop repo.
if (file_exists ( ABSPATH . 'wp-config-sample.php' ))
	$config_file = file ( ABSPATH . 'wp-config-sample.php' );
elseif (file_exists ( dirname ( ABSPATH ) . '/wp-config-sample.php' ))
	$config_file = file ( dirname ( ABSPATH ) . '/wp-config-sample.php' );
else
	wp_die ( __ ( 'Sorry, I need a wp-config-sample.php file to work from. Please re-upload this file to your WordPress installation.' ) );

// Check if wp-config.php has been created
if (file_exists ( ABSPATH . 'wp-config.php' ))
	wp_die ( '<p>' . sprintf(
					/* translators: %s: install.php */
					__ ( "The file 'wp-config.php' already exists. If you need to reset any of the configuration items in this file, please delete it first. You may try <a href='%s'>installing now</a>." ), 'install.php' ) . '</p>' );

// Check if wp-config.php exists above the root directory but is not part of another install
if (@file_exists ( ABSPATH . '../wp-config.php' ) && ! @file_exists ( ABSPATH . '../wp-settings.php' )) {
	wp_die ( '<p>' . sprintf(
						/* translators: %s: install.php */
						__ ( "The file 'wp-config.php' already exists one level above your WordPress installation. If you need to reset any of the configuration items in this file, please delete it first. You may try <a href='%s'>installing now</a>." ), 'install.php' ) . '</p>' );
}

$step = isset ( $_GET ['step'] ) ? ( int ) $_GET ['step'] : - 1;
function setup_config_display_header($body_classes = array()) {
	$body_classes = ( array ) $body_classes;
	$body_classes [] = 'wp-core-ui';
	if (is_rtl ()) {
		$body_classes [] = 'rtl';
	}
	
	header ( 'Content-Type: text/html; charset=utf-8' );
}

?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta name="viewport" content="width=device-width" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="robots" content="noindex,nofollow" />
<title><?php _e( 'WordPress &rsaquo; Setup Configuration File' ); ?></title>
	<?php
	// wp_admin_css( 'install', true );
	?>
</head>

<body class="<?php echo implode( ' ', $body_classes ); ?>">
	<p>
<?php

/* translators: %s: wp-config.php */
printf ( __ ( 'We&#8217;re going to use this information to create a %s file.' ), '<code>wp-config.php</code>' );

switch ($step) {
	case - 1 :
	// $step = -1, but we don't break here.
	case 0 :
		$step_1 = 'setup-config.php?step=1';
		
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
	<p><?php
		/* translators: %s: wp-config.php */
		printf ( __ ( 'We&#8217;re going to use this information to create a %s file.' ), '<code>wp-config.php</code>' );
		?>
	<strong><?php
		/* translators: 1: wp-config-sample.php, 2: wp-config.php */
		printf ( __ ( 'If for any reason this automatic file creation doesn&#8217;t work, don&#8217;t worry. All this does is fill in the database information to a configuration file. You may also simply open %1$s in a text editor, fill in your information, and save it as %2$s.' ), '<code>wp-config-sample.php</code>', '<code>wp-config.php</code>' );
		?></strong>
	<?php
		/* translators: %s: Codex URL */
		printf ( __ ( 'Need more help? <a href="%s">We got it</a>.' ), __ ( 'https://codex.wordpress.org/Editing_wp-config.php' ) );
		?></p>
	<p><?php _e( 'In all likelihood, these items were supplied to you by your Web Host. If you don&#8217;t have this information, then you will need to contact them before you can continue. If you&#8217;re all ready&hellip;' ); ?></p>

	<p class="step">
		<a href="<?php echo $step_1; ?>" class="button button-large"><?php _e( 'Let&#8217;s go!' ); ?></a>
	</p>
<?php
		break;
	
	case 1 :
		setup_config_display_header ();
		?>
<h1 class="screen-reader-text"><?php _e( 'Set up your database connection' ) ?></h1>
	<form method="post" action="setup-config.php?step=2">
		<p><?php _e( 'Below you should enter your database connection details. If you&#8217;re not sure about these, contact your host.' ); ?></p>
		<table class="form-table">
			<tr>
				<th scope="row"><label for="dbname"><?php _e( 'Database Name' ); ?></label></th>
				<td><input name="dbname" id="dbname" type="text" size="25"
					value="wordpress" /></td>
				<td><?php _e( 'The name of the database you want to use with WordPress.' ); ?></td>
			</tr>
			<tr>
				<th scope="row"><label for="uname"><?php _e( 'Username' ); ?></label></th>
				<td><input name="uname" id="uname" type="text" size="25"
					value="<?php echo htmlspecialchars( _x( 'username', 'example username' ), ENT_QUOTES ); ?>" /></td>
				<td><?php _e( 'Your database username.' ); ?></td>
			</tr>
			<tr>
				<th scope="row"><label for="pwd"><?php _e( 'Password' ); ?></label></th>
				<td><input name="pwd" id="pwd" type="text" size="25"
					value="<?php echo htmlspecialchars( _x( 'password', 'example password' ), ENT_QUOTES ); ?>"
					autocomplete="off" /></td>
				<td><?php _e( 'Your database password.' ); ?></td>
			</tr>
			<tr>
				<th scope="row"><label for="dbhost"><?php _e( 'Database Host' ); ?></label></th>
				<td><input name="dbhost" id="dbhost" type="text" size="25"
					value="localhost" /></td>
				<td><?php
		/* translators: %s: localhost */
		printf ( __ ( 'You should be able to get this info from your web host, if %s doesn&#8217;t work.' ), '<code>localhost</code>' );
		?></td>
			</tr>
			<tr>
				<th scope="row"><label for="prefix"><?php _e( 'Table Prefix' ); ?></label></th>
				<td><input name="prefix" id="prefix" type="text" value="wp_"
					size="25" /></td>
				<td><?php _e( 'If you want to run multiple WordPress installations in a single database, change this.' ); ?></td>
			</tr>
		</table>
	<?php if ( isset( $_GET['noapi'] ) ) { ?><input name="noapi"
			type="hidden" value="1" /><?php } ?>
	
	<p class="step">
			<input name="submit" type="submit"
				value="<?php echo htmlspecialchars( __( 'Submit' ), ENT_QUOTES ); ?>"
				class="button button-large" />
		</p>
	</form>
<?php
		break;
	
	case 2 :
		$dbname = trim ( $_POST ['dbname'] );
		$uname = trim ( $_POST ['uname'] );
		$pwd = trim ( $_POST ['pwd'] );
		$dbhost = trim ( $_POST ['dbhost'] );
		$prefix = trim ( $_POST ['prefix'] );
		
		$step_1 = 'stepup-config.php?step=1';
		$install = 'install.php';
		if (isset ( $_REQUEST ['noapi'] )) {
			$step_1 .= '&amp;noapi';
		}
		
		if (! empty ( $language )) {
			$step_1 .= '&amp;language=' . $language;
			$install .= '?language=' . $language;
		} else {
			$install .= '?language=en_US';
		}
		
		$tryagain_link = '</p><p class="step"><a href="' . $step_1 . '" onclick="javascript:history.go(-1);return false;" class="button button-large">' . __ ( 'Try again' ) . '</a>';
		
		define ( 'DB_NAME', $dbname );
		define ( 'DB_USER', $uname );
		define ( 'DB_PASSWORD', $pwd );
		define ( 'DB_HOST', $dbhost );
		
		unset ( $wpdb );
		
		global $wpdb;
		
		require_once (ABSPATH . WPINC . '/wp-db.php');
		if (file_exists ( WP_CONTENT_DIR . '/db.php' ))
			require_once (WP_CONTENT_DIR . '/db.php');
			
			if (isset ( $wpdb )) {
				return;
			}
			
			$wpdb = new wpdb ( DB_USER, DB_PASSWORD, DB_NAME, DB_HOST );
		
		/*
		 * The wpdb constructor bails when WP_SETUP_CONFIG is set, so we must
		 * fire this manually. We'll fail here if the values are no good.
		 */
		$wpdb->db_connect ();
		
		if (! empty ( $wpdb->error ))
			wp_die ( $wpdb->error . $tryagain_link );
		
		/*$wpdb->query ( "SELECT $prefix" );
		if (! $wpdb->last_error) {
			// MySQL was able to parse the prefix as a value, which we don't want. Bail.
			wp_die ( __ ( '<strong>ERROR</strong>: "Table Prefix" is invalid.' ) );
		}*/
		
		$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()-_ []{}<>~`+=,.;:/?|';
		$max = strlen ( $chars ) - 1;
		for($i = 0; $i < 8; $i ++) {
			$key = '';
			for($j = 0; $j < 64; $j ++) {
				$key .= substr ( $chars, rand ( 0, $max ), 1 );
			}
			$secret_keys [] = $key;
		}
		
		$key = 0;
		foreach ( $config_file as $line_num => $line ) {
			if ('$table_prefix  =' == substr ( $line, 0, 16 )) {
				$config_file [$line_num] = '$table_prefix  = \'' . addcslashes ( $prefix, "\\'" ) . "';\r\n";
				continue;
			}
			
			if (! preg_match ( '/^define\(\'([A-Z_]+)\',([ ]+)/', $line, $match ))
				continue;
			
			$constant = $match [1];
			$padding = $match [2];
			
			switch ($constant) {
				case 'DB_NAME' :
				case 'DB_USER' :
				case 'DB_PASSWORD' :
				case 'DB_HOST' :
					$config_file [$line_num] = "define('" . $constant . "'," . $padding . "'" . addcslashes ( constant ( $constant ), "\\'" ) . "');\r\n";
					break;
				case 'DB_CHARSET' :
					if ('utf8mb4' === $wpdb->charset || (! $wpdb->charset && $wpdb->has_cap ( 'utf8mb4' ))) {
						$config_file [$line_num] = "define('" . $constant . "'," . $padding . "'utf8mb4');\r\n";
					}
					break;
				case 'AUTH_KEY' :
				case 'SECURE_AUTH_KEY' :
				case 'LOGGED_IN_KEY' :
				case 'NONCE_KEY' :
				case 'AUTH_SALT' :
				case 'SECURE_AUTH_SALT' :
				case 'LOGGED_IN_SALT' :
				case 'NONCE_SALT' :
					$config_file [$line_num] = "define('" . $constant . "'," . $padding . "'" . $secret_keys [$key ++] . "');\r\n";
					break;
			}
		}
		unset ( $line );
		if (! is_writable ( ABSPATH )) :
			setup_config_display_header ();
			?>
<p><?php
			/* translators: %s: wp-config.php */
			printf ( __ ( 'Sorry, but I can&#8217;t write the %s file.' ), '<code>wp-config.php</code>' );
			?></p>
	<p><?php
			/* translators: %s: wp-config.php */
			printf ( __ ( 'You can create the %s manually and paste the following text into it.' ), '<code>wp-config.php</code>' );
			?></p>
	<textarea id="wp-config" cols="98" rows="15" class="code"
		readonly="readonly"><?php
			foreach ( $config_file as $line ) {
				echo htmlentities ( $line, ENT_COMPAT, 'UTF-8' );
			}
			?></textarea>
	<p><?php _e( 'After you&#8217;ve done that, click &#8220;Run the install.&#8221;' ); ?></p>
	<p class="step">
		<a href="<?php echo $install; ?>" class="button button-large"><?php _e( 'Run the install' ); ?></a>
	</p>
	<script>
(function(){
if ( ! /iPad|iPod|iPhone/.test( navigator.userAgent ) ) {
	var el = document.getElementById('wp-config');
	el.focus();
	el.select();
}
})();
</script>

<?php
		else :
			/*
			 * If this file doesn't exist, then we are using the wp-config-sample.php
			 * file one level up, which is for the develop repo.
			 */
			if (file_exists ( ABSPATH . 'wp-config-sample.php' ))
				$path_to_wp_config = ABSPATH . 'wp-config.php';
			else
				$path_to_wp_config = dirname ( ABSPATH ) . '/wp-config.php';
			
			$handle = fopen ( $path_to_wp_config, 'w' );
			foreach ( $config_file as $line ) {
				fwrite ( $handle, $line );
			}
			fclose ( $handle );
			chmod ( $path_to_wp_config, 0666 );
			setup_config_display_header ();
			?>
<h1 class="screen-reader-text"><?php _e( 'Successful database connection' ) ?></h1>
	<p><?php _e( 'All right, sparky! You&#8217;ve made it through this part of the installation. WordPress can now communicate with your database. If you are ready, time now to&hellip;' ); ?></p>

	<p class="step">
		<a href="<?php echo $install; ?>" class="button button-large"><?php _e( 'Run the install' ); ?></a>
	</p>

<?php 
	endif;
	break;
}
?>	

</body>
</html>