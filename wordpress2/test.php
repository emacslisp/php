<?php

//phpinfo();
function test_func( $text, $var1, $var2 ){
	return $text . $var1 . $var2;
}

$link = mysql_connect('127.0.0.1:3307', 'root', '123456');
if (!$link) {
	die('Could not connect: ' . mysql_error());
}
printf("MySQL host info: %s\n", mysql_get_host_info());

mysql_close($link);

require('./wp-includes/wp-db.php');
/*require('./wp-includes/class-wp-hook.php');

$hook = new WP_Hook();

$hook->add_filter("test", "test_func", 10, 3);

echo $hook->apply_filters("test", 'arg1','arg2','arg3');*/


$wpdb = new wpdb('root','123456','wordpress','127.0.0.1:3307');

$wpdb->db_connect();

echo "<br> wpdb version: ";

echo $wpdb-> db_version();

echo "<br> wpdb users: ";

echo $wpdb->get_var('select * from wp_users');

$wpdb->close();



?>