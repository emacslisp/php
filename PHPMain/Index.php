<?php

//@example: php constant using string
define("CONSTANT", "Hello world.");

echo CONSTANT . "<br/>";

$x = 1;

echo ++$x;

//@example: phpversion api
echo 'Current PHP version: ' . phpversion(). "<br>";

//@example: php ini_get
echo 'display_errors = ' . ini_get('display_errors') . "<br>";
echo 'register_globals = ' . ini_get('register_globals') . "<br>";
echo 'post_max_size = ' . ini_get('post_max_size') . "<br>";
echo 'post_max_size+1 = ' . (ini_get('post_max_size')+1) . "<br>";
echo 'post_max_size in bytes = ' . ini_get('post_max_size') ."<br>";


//@example: php version_compare
if (version_compare(phpversion(), '5.3.10', '<')) {
	echo("php version isn't high enough");
}
else 
	echo 'php version is high enough';

	echo '<br>';
	
	//@example: php init_get and init_set
	@ini_set( 'magic_quotes_runtime', 0 );
	$xx = ini_get( 'magic_quotes_runtime');
	echo $xx;
	
	echo ini_get( 'magic_quotes_runtime');
	@ini_set( 'magic_quotes_runtime', $xx );
?>