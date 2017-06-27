<?php

function d() {
	var_dump(__FILE__.':('.__LINE__.') '.__FUNCTION__.PHP_EOL.(new Exception)->getTraceAsString());
	debug_print_backtrace();
	echo PHP_EOL;
	var_dump(debug_backtrace());
	error_log(var_dump((new Exception)->getTraceAsString(), true));
	file_put_contents('/Users/ewu/output.log',print_r(__FILE__.':('.__LINE__.') '.__FUNCTION__.PHP_EOL.(new Exception)->getTraceAsString(),true). PHP_EOL . PHP_EOL,FILE_APPEND);
}

function c() {
	d();
}

function b() {
	c();
}

function printTest($string) {
	b();
	echo $string;
}

//@todo: adding date time here for php
//phpinfo();


printTest();



?>

<?php 
file_put_contents('/Users/ewu/output.log',print_r((new Exception)->getTraceAsString(),true). PHP_EOL . PHP_EOL,FILE_APPEND);

?>