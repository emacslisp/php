<?php

//@important: put sock file here rather that port number
/*
 * @example: 
 * */
$link = mysqli_connect('127.0.0.1:3306', 'root', '123456')
//$link = mysql_connect('127.0.0.1:3307', 'root', '123456')

or die('Could not connect: ' . mysqli_error());
echo 'Connected successfully';
mysqli_select_db($link, 'test') or die('Could not select database');

// Performing SQL query
$query = 'SELECT * FROM User';
$result = mysqli_query($link, $query) or die('Query failed: ' . mysqli_error($link));

// Printing results in HTML
echo "<table>\n";
while ($line = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
	echo "\t<tr>\n";
	foreach ($line as $col_value) {
		echo "\t\t<td>$col_value</td>\n";
	}
	echo "\t</tr>\n";
}
echo "</table>\n";

// Free resultset
mysqli_free_result($result);

// Closing connection
mysqli_close($link);

?>