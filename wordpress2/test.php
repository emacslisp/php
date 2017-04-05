<?php

require( dirname(__FILE__) . '/wp-load.php' );

?>

<?php


function print_filters_for( $hook = '' ) {
	global $wp_filter;
	if( empty( $hook ) || !isset( $wp_filter[$hook] ) )
		return 'temp define';

		print '<pre>';
		print_r( $wp_filter[$hook] );
		print '</pre>';
}

print_filters_for( 'wp_head' );


printf( __( 'Proudly powered by %s', 'twentyseventeen' ), 'WordPress' );

$isFrontPage = is_front_page();

echo $isFrontPage;

//exit;
?>

<?php 

//@example: php wordpress - get_userdata
 $user_info = get_userdata( 1);
 echo 'Username: ' . $user_info-> user_login . "\ n"; 
 echo 'User roles: ' . implode(', ', $user_info-> roles) . "\ n"; echo 'User ID: ' . $user_info-> ID . "\ n";


 //exit;

?>

<?php


function test_func( $text, $var1, $var2 ){
	return $text . $var1 . $var2;
}

function test_func2( $text, $var1, $var2 ){
	return $text . $var1 . $var2;
}


 echo "###### " . $wpdb->db_version();
  
 echo "<br> wpdb version: ";
 echo $wpdb-> db_version();
 echo "<br> wpdb users: ";
 echo $wpdb->get_var('select * from wp_users');
 $wpdb->close();

//@example: php - wordpress - add_filter, apply_filters, add_action, do_action example
add_filter("test", "test_func", 10, 3);

$value =  apply_filters("test",'arg1','arg2','arg3'); 
 echo $value;
 
 echo '<br> it is working xxx ';
 
 add_action("test_action","test_func2",10,3);
 echo '<br> do_action test: ';
 echo do_action("test_action",'arg1','arg2','arg3');
 
 //wp_redirect("http://www.google.com.au");

 
?>    