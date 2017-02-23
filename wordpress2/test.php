<?php

//phpinfo();


$link = mysql_connect('127.0.0.1:3307', 'root', '123456');
if (!$link) {
	die('Could not connect: ' . mysql_error());
}
printf("MySQL host info: %s\n", mysql_get_host_info());

?>