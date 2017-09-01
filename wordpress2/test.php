<?php

require( './wp-load.php' );

?>

<?php 

function print_filters_for1( $hook = '' ) {
	global $wp_filter;
	if( empty( $hook ) || !isset( $wp_filter[$hook] ) )
		return;
		
		print '<pre>';
		print_r( $wp_filter[$hook] );
		print '</pre>';
}

print (new ReflectionFunction("_wp_admin_bar_init"))->getFileName();
print '<br/>';

print '<p>==============</p><br/>';

print_filters_for('style_loader_src');

function list_all() {
global $wp_filter;

$comment_filters = array ();
$h1  = '<h1>Current Comment Filters</h1>';
$out = '';
$toc = '<ul>';

foreach ( $wp_filter as $key => $val )
{
	if ( FALSE !== strpos( $key, 'comment' ) )
	{
		$comment_filters[$key][] = var_export( $val, TRUE );
	}
}

foreach ( $comment_filters as $name => $arr_vals )
{
	$out .= "<h2 id=$name>$name</h2><pre>" . implode( "\n\n", $arr_vals ) . '</pre>';
	$toc .= "<li><a href='#$name'>$name</a></li>";
}

print "$h1$toc</ul>$out";
}

?>

<?php

echo('wp-load.php');

echo '__DIR__'.__DIR__;

echo '<br/>';

echo 'dirname(__FILE__)'.dirname(__FILE__);

$post = $wpdb->prepare( "SELECT * FROM $wpdb->posts WHERE ID = %d LIMIT 1", 4 );
//$post = WP_POST::get_instance(4);

$post_final = $wpdb->get_row($post);



function print_filters_for( $hook = '' ) {
	global $wp_filter;
	if( empty( $hook ) || !isset( $wp_filter[$hook] ) )
		return 'temp define';

		print '<pre>';
		print_r( $wp_filter[$hook] );
		print '</pre>';
}

print_filters_for( 'template_redirect' );


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