
<?php

define('WP_INSTALLING', true);

define('WP_SETUP_CONFIG', true);

function is_rtl() {
	return false;
}

function _e($text, $domain='default') {
	echo $text;
}

function __($text, $domain='default') {
	return $text;
}

function _x($text) {
	return $text;
}

$step = isset( $_GET['step'] ) ? (int) $_GET['step'] : -1;

function setup_config_display_header( $body_classes = array() ) {
	$body_classes = (array) $body_classes;
	$body_classes[] = 'wp-core-ui';
	if ( is_rtl() ) {
		$body_classes[] = 'rtl';
	}
	
	header( 'Content-Type: text/html; charset=utf-8' );
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
	//wp_admin_css( 'install', true ); 
	?>
</head>

<body class="<?php echo implode( ' ', $body_classes ); ?>">
<p>
<?php 

/* translators: %s: wp-config.php */
printf( __( 'We&#8217;re going to use this information to create a %s file.' ),
'<code>wp-config.php</code>'
			);


switch($step) {
	case -1:
		// $step = -1, but we don't break here.
	case 0:
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
	printf( __( 'We&#8217;re going to use this information to create a %s file.' ),
		'<code>wp-config.php</code>'
	);
	?>
	<strong><?php
		/* translators: 1: wp-config-sample.php, 2: wp-config.php */
		printf( __( 'If for any reason this automatic file creation doesn&#8217;t work, don&#8217;t worry. All this does is fill in the database information to a configuration file. You may also simply open %1$s in a text editor, fill in your information, and save it as %2$s.' ),
			'<code>wp-config-sample.php</code>',
			'<code>wp-config.php</code>'
		);
	?></strong>
	<?php
	/* translators: %s: Codex URL */
	printf( __( 'Need more help? <a href="%s">We got it</a>.' ),
		__( 'https://codex.wordpress.org/Editing_wp-config.php' )
	);
?></p>
<p><?php _e( 'In all likelihood, these items were supplied to you by your Web Host. If you don&#8217;t have this information, then you will need to contact them before you can continue. If you&#8217;re all ready&hellip;' ); ?></p>

<p class="step"><a href="<?php echo $step_1; ?>" class="button button-large"><?php _e( 'Let&#8217;s go!' ); ?></a></p>
<?php
	break;
	
case 1:
	setup_config_display_header();
?>
<h1 class="screen-reader-text"><?php _e( 'Set up your database connection' ) ?></h1>
<form method="post" action="setup-config.php?step=2">
	<p><?php _e( 'Below you should enter your database connection details. If you&#8217;re not sure about these, contact your host.' ); ?></p>
	<table class="form-table">
		<tr>
			<th scope="row"><label for="dbname"><?php _e( 'Database Name' ); ?></label></th>
			<td><input name="dbname" id="dbname" type="text" size="25" value="wordpress" /></td>
			<td><?php _e( 'The name of the database you want to use with WordPress.' ); ?></td>
		</tr>
		<tr>
			<th scope="row"><label for="uname"><?php _e( 'Username' ); ?></label></th>
			<td><input name="uname" id="uname" type="text" size="25" value="<?php echo htmlspecialchars( _x( 'username', 'example username' ), ENT_QUOTES ); ?>" /></td>
			<td><?php _e( 'Your database username.' ); ?></td>
		</tr>
		<tr>
			<th scope="row"><label for="pwd"><?php _e( 'Password' ); ?></label></th>
			<td><input name="pwd" id="pwd" type="text" size="25" value="<?php echo htmlspecialchars( _x( 'password', 'example password' ), ENT_QUOTES ); ?>" autocomplete="off" /></td>
			<td><?php _e( 'Your database password.' ); ?></td>
		</tr>
		<tr>
			<th scope="row"><label for="dbhost"><?php _e( 'Database Host' ); ?></label></th>
			<td><input name="dbhost" id="dbhost" type="text" size="25" value="localhost" /></td>
			<td><?php
				/* translators: %s: localhost */
				printf( __( 'You should be able to get this info from your web host, if %s doesn&#8217;t work.' ),'<code>localhost</code>' );
			?></td>
		</tr>
		<tr>
			<th scope="row"><label for="prefix"><?php _e( 'Table Prefix' ); ?></label></th>
			<td><input name="prefix" id="prefix" type="text" value="wp_" size="25" /></td>
			<td><?php _e( 'If you want to run multiple WordPress installations in a single database, change this.' ); ?></td>
		</tr>
	</table>
	<?php if ( isset( $_GET['noapi'] ) ) { ?><input name="noapi" type="hidden" value="1" /><?php } ?>
	
	<p class="step"><input name="submit" type="submit" value="<?php echo htmlspecialchars( __( 'Submit' ), ENT_QUOTES ); ?>" class="button button-large" /></p>
</form>
<?php 	
	break;
	
case 2:
	$dbname = trim($_POST['dbname']);
	$uname = trim($_POST['uname']);
	$pwd = trim($_POST['pwd']);
	$dbhost = trim($_POST['dbhost']);
	$prefix = trim($_POST['prefix']);
	
	$step_1 = 'stepup-config.php?step=1';
	$install = 'install.php';
	if ( isset( $_REQUEST['noapi'] ) ) {
		$step_1 .= '&amp;noapi';
	}
	
	if ( ! empty( $language ) ) {
		$step_1 .= '&amp;language=' . $language;
		$install .= '?language=' . $language;
	} else {
		$install .= '?language=en_US';
	}
	
	$tryagain_link = '</p><p class="step"><a href="' . $step_1 . '" onclick="javascript:history.go(-1);return false;" class="button button-large">' . __( 'Try again' ) . '</a>';
	
	define('DB_NAME', $dbname);
	define('DB_USER', $uname);
	define('DB_PASSWORD', $pwd);
	define('DB_HOST', $dbhost);
	
	unset( $wpdb );
	require_wp_db();
	
	/*
	 * The wpdb constructor bails when WP_SETUP_CONFIG is set, so we must
	 * fire this manually. We'll fail here if the values are no good.
	 */
	$wpdb->db_connect();
	
	break;
}
?>	

</body>
</html>