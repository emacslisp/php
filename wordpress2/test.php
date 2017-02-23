<?php

require( dirname(__FILE__) . '/wp-load.php' );



?>

<?php


function test_func( $text, $var1, $var2 ){
	return $text . $var1 . $var2;
}


 echo "###### " . $wpdb->db_version();
  
 echo "<br> wpdb version: ";
 echo $wpdb-> db_version();
 echo "<br> wpdb users: ";
 echo $wpdb->get_var('select * from wp_users');
 $wpdb->close();


add_filter("test", "test_func", 10, 3);

$value =  apply_filters("test", 'arg1','arg2','arg3'); 
 echo $value;
 
 echo '<br> it is working';
?>    