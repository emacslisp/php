<?php

// @example: wordpress core - simulate add_doaction and apply_doaction
function test() {
	echo 'hello world xxxx !!!!';
}

$priority = 0;
$function_to_add='test';
$idx = $function_to_add;
$accepted_args= '';

$callbacks = array();

$callbacks[$priority][$idx] = array(
			'function' => $function_to_add,
			'accepted_args' => $accepted_args
		);

call_user_func_array($callbacks[$priority][$idx]['function'],array());

?>