<?php
debug_print_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
file_put_contents('/Users/ewu/output.log',print_r((new Exception)->getTraceAsString(),true). PHP_EOL . PHP_EOL,FILE_APPEND);

function d() {
	var_dump((new Exception)->getTraceAsString());
	debug_print_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
	error_log(var_export((new Exception)->getTraceAsString(), true));
	file_put_contents('/Users/ewu/output.log',print_r((new Exception)->getTraceAsString(),true). PHP_EOL . PHP_EOL,FILE_APPEND);
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