<?php

/*
 * Be careful: 
printf ("(9.95 * 100) = %d \n", (9.95 * 100)); 

'994' 

First %d converts a float to an int by truncation. 

Second floats are notorious for tiny little rounding errors.
 * */

printf ("(9.95 * 100) = %d \n", (9.95 * 100)); 
?>