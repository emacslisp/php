<?php

$str = "A 'quote' is <b>bold</b>";

// Outputs: A 'quote' is &lt;b&gt;bold&lt;/b&gt;
echo htmlentities($str);

// Outputs: A &#039;quote&#039; is &lt;b&gt;bold&lt;/b&gt;
echo htmlentities($str, ENT_QUOTES);


$str = "\x8F!!!";
echo "==================<br>";
// Outputs an empty string
echo htmlentities($str, ENT_QUOTES, "UTF-8");

echo "==================<br>";
// Outputs "!!!"
echo htmlentities($str, ENT_QUOTES | ENT_IGNORE, "UTF-8");

?>