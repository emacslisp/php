<?php
$var = '';

// This will evaluate to TRUE so the text will be printed.
if (isset($var)) {
	echo "This var is set so I will print.";
}

unset($var);

if (isset($var)) {
	echo "This var is set so I will print.";
}
else echo "\$var is unset";

?>