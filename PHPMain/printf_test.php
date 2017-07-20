<?php

/*
 * Be careful: 
printf ("(9.95 * 100) = %d \n", (9.95 * 100)); 

'994' 

First %d converts a float to an int by truncation. 

Second floats are notorious for tiny little rounding errors.
 * */

function __($text) {
	return $text;
}

printf ("(9.95 * 100) = %d \n", (9.95 * 100)); 

printf(__("test %s"), '<b> printf Test</b>');

$prefix = 'wordpress';
echo "Select $prefix";
?>